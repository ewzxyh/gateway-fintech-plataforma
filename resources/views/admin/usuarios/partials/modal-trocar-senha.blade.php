@props([
  'user'
])


<div class="modal fade glassmorphism-password-modal" id="trocarSenhaModal-{{ $user->id }}" tabindex="-1" aria-labelledby="trocarSenhaModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.usuarios.change-password', ['id' => $user->id]) }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="trocarSenhaModalLabel-{{ $user->id }}">
            <i class="fas fa-key me-2"></i>Alterar Senha
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editUserId" name="id">
          <div class="mb-3">
            <label for="editSenha-{{ $user->id }}" class="form-label">
              <i class="fas fa-lock me-2"></i>Nova Senha
            </label>
            <div class="password-container position-relative">
              <input type="password" 
                  class="form-control @error('password') is-invalid @enderror" 
                  id="editSenha-{{ $user->id }}" 
                  name="password" 
                  placeholder="Digite a nova senha" 
                  required>
              <button type="button" 
                  class="password-toggle position-absolute" 
                  onclick="togglePassword('editSenha-{{ $user->id }}')" 
                  style="right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: rgba(255, 255, 255, 0.7);">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <!-- Requisitos da Senha -->
            <div class="password-requirements mt-2" id="passwordRequirements-{{ $user->id }}" style="display: none;">
              <h6 class="text-white mb-2">Requisitos da senha:</h6>
              <ul class="list-unstyled mb-0">
                <li id="req-length-{{ $user->id }}">Pelo menos 8 caracteres</li>
                <li id="req-lowercase-{{ $user->id }}">Uma letra minúscula (a-z)</li>
                <li id="req-uppercase-{{ $user->id }}">Uma letra maiúscula (A-Z)</li>
                <li id="req-number-{{ $user->id }}">Um número (0-9)</li>
                <li id="req-special-{{ $user->id }}">Um caractere especial (@$!%*?&+#^~`|/:";'<>,.=-_[]{}())</li>
              </ul>
            </div>
            
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-save me-1"></i>Salvar Alterações
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Password validation for admin modal
  function validatePassword(password) {
    const requirements = {
      length: password.length >= 8,
      lowercase: /[a-z]/.test(password),
      uppercase: /[A-Z]/.test(password),
      number: /\d/.test(password),
      special: /[@$!%*?&+#^~`|\\/:";'<>,.=\-_\[\]{}()]/.test(password)
    };
    
    return requirements;
  }

  function updatePasswordRequirements(password, userId) {
    const requirements = validatePassword(password);
    const requirementsDiv = document.getElementById(`passwordRequirements-${userId}`);
    
    // Show/hide requirements div
    if (password.length > 0) {
      requirementsDiv.style.display = 'block';
    } else {
      requirementsDiv.style.display = 'none';
    }
    
    // Update each requirement
    Object.keys(requirements).forEach((key, index) => {
      const element = document.getElementById(`req-${key}-${userId}`);
      if (element) {
        if (requirements[key]) {
          element.classList.add('valid');
        } else {
          element.classList.remove('valid');
        }
      }
    });
  }

  // Password toggle function
  function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Add event listeners when modal is shown
  document.addEventListener('DOMContentLoaded', function() {
    // Listen for modal show events
    document.querySelectorAll('[data-bs-target^="#trocarSenhaModal-"]').forEach(button => {
      button.addEventListener('click', function() {
        const modalId = this.getAttribute('data-bs-target').replace('#', '');
        const userId = modalId.replace('trocarSenhaModal-', '');
        
        // Add event listener to password input when modal is shown
        setTimeout(() => {
          const passwordInput = document.getElementById(`editSenha-${userId}`);
          if (passwordInput) {
            passwordInput.addEventListener('input', function(e) {
              updatePasswordRequirements(e.target.value, userId);
            });
            
            passwordInput.addEventListener('focus', function(e) {
              if (e.target.value.length > 0) {
                updatePasswordRequirements(e.target.value, userId);
              }
            });
          }
        }, 100);
      });
    });
  });
</script>
