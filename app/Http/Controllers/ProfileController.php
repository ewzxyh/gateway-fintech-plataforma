<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Traits\IPManagementTrait;
use App\Traits\PinManagementTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use PinManagementTrait;

    public function index(Request $request)
    {
        $setting = \App\Models\App::first();
        $user = auth()->user();
        
        // Calcular taxas para exibição (prioridade: usuário > global)
        $taxas = \App\Helpers\TaxaDisplayHelper::getTaxasParaExibicao($user, $setting);
        
        return view('profile.perfil', compact('setting', 'taxas'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Atualizar campos permitidos
        $user->update($request->only([
            'name', 'email', 'telefone', 'data_nascimento',
            'cep', 'rua', 'numero_residencia', 'complemento',
            'bairro', 'cidade', 'estado'
        ]));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        return Redirect::route('profile.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();
        
        // Validação rigorosa do arquivo
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048'
        ]);
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Validação adicional de segurança
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                Log::warning('Tentativa de upload de avatar com extensão não permitida', [
                    'extension' => $extension,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id
                ]);
                return redirect()->back()->with('error', 'Tipo de arquivo não permitido. Use apenas JPG, PNG, GIF ou WebP.');
            }
            
            // Verificar MIME type para prevenir arquivos maliciosos
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType = $file->getMimeType();
            
            if (!in_array($mimeType, $allowedMimes)) {
                Log::warning('Tentativa de upload de avatar com MIME type suspeito', [
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id
                ]);
                return redirect()->back()->with('error', 'Arquivo inválido detectado.');
            }
            
            // Verificar conteúdo do arquivo para detectar PHP disfarçado
            $fileContent = file_get_contents($file->getRealPath());
            if (strpos($fileContent, '<?php') !== false || 
                strpos($fileContent, '<?=') !== false || 
                strpos($fileContent, '<script') !== false) {
                Log::critical('Tentativa de upload de avatar malicioso detectada', [
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id,
                    'file_size' => $file->getSize()
                ]);
                return redirect()->back()->with('error', 'Arquivo malicioso detectado e bloqueado!');
            }
            
            // Renomear arquivo com UUID seguro
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            $destination = public_path('uploads/avatars');
            
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            
            if ($file->move($destination, $filename)) {
                // Definir permissões seguras
                chmod($destination . '/' . $filename, 0644);
                
                $user->avatar = '/uploads/avatars/' . $filename;
                $user->save();
                
                Log::info('Avatar atualizado com sucesso', [
                    'filename' => $filename,
                    'ip' => $request->ip(),
                    'user_id' => $user->id
                ]);
                
                return redirect()->back()->with('success', 'Avatar atualizado com sucesso!');
            } else {
                Log::error('Erro ao mover arquivo de avatar', [
                    'ip' => $request->ip(),
                    'user_id' => $user->id
                ]);
                return redirect()->back()->with('error', 'Erro ao salvar o arquivo.');
            }
        } else {
            return redirect()->back()->with('error', 'Não foi possível alterar o avatar. Tente novamente!');
        }
    }

    /**
     * Adiciona um IP à lista de permitidos
     */
    public function addAllowedIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:45'
        ]);

        $user = Auth::user();
        $ip = $request->ip;

        // Validar formato do IP
        if (!IPManagementTrait::isValidIP($ip)) {
            return redirect()->back()->with('error', 'Formato de IP inválido. Use formato: 192.168.1.1, 192.168.1.0/24 ou 192.168.1.*');
        }

        if (IPManagementTrait::addAllowedIP($user, $ip)) {
            return redirect()->back()->with('success', 'IP adicionado com sucesso!');
        } else {
            return redirect()->back()->with('error', 'IP já existe na lista ou erro ao adicionar.');
        }
    }

    /**
     * Remove um IP da lista de permitidos
     */
    public function removeAllowedIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:45'
        ]);

        $user = Auth::user();
        $ip = $request->ip;

        if (IPManagementTrait::removeAllowedIP($user, $ip)) {
            return redirect()->back()->with('success', 'IP removido com sucesso!');
        } else {
            return redirect()->back()->with('error', 'Erro ao remover IP.');
        }
    }

    /**
     * Lista IPs permitidos (API)
     */
    public function getAllowedIPs()
    {
        $user = Auth::user();
        $ips = IPManagementTrait::getAllowedIPs($user);
        
        return response()->json([
            'success' => true,
            'ips' => $ips
        ]);
    }

    /**
     * Cria um PIN para o usuário
     */
    public function createPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $user = Auth::user();
        $result = $this->createPinInternal($user, $request->pin);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message'])->with('pin_created', $result['pin']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Altera o PIN do usuário
     */
    public function changePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|size:6|regex:/^\d{6}$/',
            'new_pin' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $user = Auth::user();
        $result = $this->changePinInternal($user, $request->current_pin, $request->new_pin);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Ativa/Desativa o PIN
     */
    public function togglePin(Request $request)
    {
        $request->validate([
            'active' => 'required|boolean'
        ]);

        $user = Auth::user();
        $result = $this->togglePinStatus($user, $request->active);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Remove o PIN do usuário
     */
    public function removePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $user = Auth::user();
        $result = $this->removePinInternal($user, $request->current_pin);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Verifica PIN (API)
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $user = Auth::user();
        $isValid = $this->verifyPinInternal($user, $request->pin);

        return response()->json([
            'success' => $isValid,
            'message' => $isValid ? 'PIN válido' : 'PIN inválido'
        ]);
    }
}
