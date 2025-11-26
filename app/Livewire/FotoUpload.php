<?php

namespace App\Livewire;

use Illuminate\Contracts\Queue\Job;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FotoUpload extends Component
{
    use WithFileUploads;

    public $image;

    protected $rules = [
        'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
    ];

    protected $messages = [
        'image.required' => 'Por favor, selecione uma imagem.',
        'image.image' => 'O arquivo deve ser uma imagem válida.',
        'image.mimes' => 'Apenas arquivos JPG, PNG, GIF e WebP são permitidos.',
        'image.max' => 'A imagem não pode ser maior que 2MB.',
    ];

    public function updatedImage()
    {
        $this->validateOnly('image');
    }

    public function upload()
    {
        $this->validate();

        try {
            $file = $this->image;
            
            // Validação rigorosa de segurança
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                Log::warning('Tentativa de upload de arquivo não permitido', [
                    'extension' => $extension,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                session()->flash('error', 'Tipo de arquivo não permitido: ' . $extension);
                return;
            }
            
            // Verificar MIME type para prevenir arquivos maliciosos
            $allowedMimes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
            ];
            
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $allowedMimes)) {
                Log::warning('Tentativa de upload com MIME type suspeito', [
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                session()->flash('error', 'Tipo MIME não permitido: ' . $mimeType);
                return;
            }
            
            // Verificar conteúdo do arquivo para detectar PHP disfarçado
            $fileContent = file_get_contents($file->getRealPath());
            if (strpos($fileContent, '<?php') !== false || 
                strpos($fileContent, '<?=') !== false || 
                strpos($fileContent, '<script') !== false) {
                Log::critical('Tentativa de upload de arquivo malicioso detectada', [
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'file_size' => $file->getSize()
                ]);
                session()->flash('error', 'Arquivo malicioso detectado e bloqueado!');
                return;
            }
            
            // Renomear arquivo com UUID seguro
            $filename = Str::uuid() . '.' . $extension;
            $destination = public_path('uploads');
            
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $path = $file->move($destination, $filename);
            
            // Definir permissões seguras
            chmod($destination . '/' . $filename, 0644);

            session()->flash('success', 'Imagem enviada com sucesso!');
            
            Log::info('Upload de imagem realizado com sucesso', [
                'filename' => $filename,
                'ip' => request()->ip(),
                'user_id' => auth()->id()
            ]);
            
        } catch (\Throwable $e) {
            Log::error('Erro no upload de imagem', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
                'user_id' => auth()->id()
            ]);
            session()->flash('error', 'Erro ao salvar imagem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.foto-upload');
    }
}
