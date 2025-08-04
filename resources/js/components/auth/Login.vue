<!-- <template>
    <div class="login-container">
        <div class="left-panel d-none d-lg-block">
            <div class="image-section">
                <img :src="assetPath('images/back_1.jpg')" alt="Welcome" class="welcome-image">
                <div class="welcome-overlay">
                    <div class="welcome-content">
                        <div class="brand-section">
                            <div class="brand-icon">
                                <i class="bi bi-shop"></i>
                            </div>
                            <h1 class="brand-title">ShopMaster</h1>
                        </div>

                        <h2 class="welcome-title">Welcome Back!</h2>
                        <p class="welcome-subtitle">Continue your amazing e-commerce journey with us.</p>

                        <div class="welcome-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Exclusive Deals</h4>
                                    <p>Special offers for returning customers</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Fast Shipping</h4>
                                    <p>Get your orders delivered quickly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <div class="mobile-header d-lg-none">
                    <div class="mobile-brand">
                        <i class="bi bi-shop"></i>
                        <span>ShopMaster</span>
                    </div>
                    <h2 class="mobile-title">Welcome Back</h2>
                    <p class="mobile-subtitle">Login to your account</p>
                </div>

                <div class="desktop-header d-none d-lg-block">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Login to continue to your account</p>
                </div>

                <div v-if="autoLoginLoading" class="alert alert-info">
                    <div class="alert-content">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <h5>Auto Login</h5>
                            <p>Logging in with saved credentials...</p>
                        </div>
                    </div>
                    <span class="spinner"></span>
                </div>

                <div v-if="showErrorAlert" class="alert alert-danger">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <h5>Login Failed</h5>
                            <p>{{ errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click="showErrorAlert = false"></button>
                </div>

                <form @submit.prevent="handleLogin" class="login-form">
                    <div class="input-group">
                        <label class="input-label">Email or Username</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-input" v-model="form.login" 
                                @input="validateLogin" maxlength="20"
                                placeholder="Enter your email or username">
                        </div>
                        <div class="error-message" v-if="errors.login">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.login }}
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input :type="showPassword ? 'text' : 'password'" class="form-input" 
                                v-model="form.password" @input="validatePassword" maxlength="32"
                                placeholder="Enter your password">
                            <i class="bi password-toggle-icon" 
                               :class="showPassword ? 'bi-eye-fill':'bi-eye-slash-fill' " 
                               @click="showPassword = !showPassword"></i>
                        </div>
                        <div class="error-message" v-if="errors.password">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.password }}
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" v-model="form.remember">
                            <label for="remember">Remember me</label>
                        </div>
                       
                    </div>

                    <button type="submit" class="login-btn" :disabled="loading || autoLoginLoading">
                        <span v-if="!loading">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login
                        </span>
                        <span v-else>
                            <span class="spinner"></span>
                            Logging in...
                        </span>
                    </button>

                    <div class="auth-footer">
                        <p>Don't have an account? <router-link to="/signup" class="auth-link">Sign up</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { useAdminStore } from '../../stores/adminStore';
import { useCustomerStore } from '../../stores/customerStore';

export default {
    data() {
        return {
            form: {
                login: '',
                password: '',
                remember: false
            },
            errors: {
                login: '',
                password: ''
            },
            loading: false,
            autoLoginLoading: false,
            showErrorAlert: false,
            errorMessage: '',
            showPassword: false
        }
    },
    created() {
        this.checkSavedCredentials();
    },
    methods: {
        assetPath(path) {
            return `/${path}`;
        },
        validateLogin() {
            if (!this.form.login) {
                this.errors.login = 'The email or username field is required.';
            } else if (this.form.login.length > 20) {
                this.errors.login = 'Username must be less than 20 characters.';
            } else {
                this.errors.login = '';
            }
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'The password field is required.';
            } else if (this.form.password.length > 32) {
                this.errors.password = 'Password must be less than 32 characters.';
            } else {
                this.errors.password = '';
            }
        },
        checkSavedCredentials() {
            const savedCredentials = localStorage.getItem('savedCredentials');
            if (savedCredentials) {
                try {
                    const credentials = JSON.parse(savedCredentials);
                    this.form.login = credentials.login;
                    this.form.password = credentials.password;
                    this.form.remember = true;
                    
                    this.autoLoginLoading = true;
                    
                    setTimeout(() => {
                        this.handleLogin();
                    }, 500);
                } catch (e) {
                    console.error('Failed to parse saved credentials', e);
                }
            }
        },
        saveCredentials() {
            if (this.form.remember) {
                const credentials = {
                    login: this.form.login,
                    password: this.form.password
                };
                localStorage.setItem('savedCredentials', JSON.stringify(credentials));
            } else {
                localStorage.removeItem('savedCredentials');
            }
        },
        validateForm() {
            this.validateLogin();
            this.validatePassword();
            return !this.errors.login && !this.errors.password;
        },
        async handleLogin() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.showErrorAlert = false;
            
            try {
                const response = await axios.post('/login', this.form);
                console.log('Login API Response:', response.data);
                
                if (response.data.success) {
                    this.saveCredentials();
                    
                    const role = response.data.role;

                    if (role === 'Admin') {
                        const adminStore = useAdminStore();
                        adminStore.setPermissions(response.data.permissions);
                        adminStore.setAdminData(response.data.admin);
                        setTimeout(() => {
                            window.location.href = '/admin/home';
                        }, 10);
                    } else if (role === 'Customer') {
                        const customerStore = useCustomerStore();
                        customerStore.setCustomerData(response.data.customer);
                        setTimeout(() => {
                            window.location.href = '/customer/home';
                        }, 10);
                    } else {
                        this.errorMessage = 'Role not supported';
                        this.showErrorAlert = true;
                    }
                } else {
                    this.errorMessage = response.data.message || 'Login failed. Please try again.';
                    this.showErrorAlert = true;
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.errorMessage = 'Please fix the validation errors';
                    } else if (error.response.status === 401) {
                        this.errorMessage = 'Invalid credentials. Please try again.';
                    } else if (error.response.status === 403) {
                        this.errorMessage = 'You are not authorized to access this system.';
                    } else {
                        this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
                    }
                } else {
                    this.errorMessage = 'Network error. Please check your connection and try again.';
                }
                this.showErrorAlert = true;
                console.error('Login error:', error);
            } finally {
                this.loading = false;
                this.autoLoginLoading = false;
            }
        }
    }
}
</script>

<style scoped>
.login-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.left-panel {
    width: 50%;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    overflow: hidden;
}

.image-section {
    width: 100%;
    height: 100%;
    position: relative;
}

.welcome-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.welcome-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.welcome-content {
    max-width: 420px;
    text-align: center;
    color: white;
}

.brand-section {
    margin-bottom: 2rem;
}

.brand-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.brand-icon i {
    font-size: 1.8rem;
    color: white;
}

.brand-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.welcome-subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.5;
}

.welcome-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: left;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.1rem;
    color: white;
}

.feature-content h4 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.2rem 0;
}

.feature-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

/* Right Panel Styles */
.right-panel {
    width: 50%;
    margin-left: 50%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.form-container {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.mobile-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1e293b;
}

.mobile-brand i {
    font-size: 1.8rem;
    color: #3b82f6;
}

.mobile-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.mobile-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.mobile-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.desktop-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.alert {
    border: none;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.alert-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.alert-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    flex-grow: 1;
}

.alert-content i {
    font-size: 1.5rem;
}

.alert-content h5 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.alert-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1rem;
    padding: 0;
    cursor: pointer;
    margin-left: 0.5rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.input-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    font-size: 1rem;
}

.password-toggle-icon {
    position: absolute;
    right: 1rem;
    color: #9ca3af;
    font-size: 1.1rem;
    cursor: pointer;
    transition: color 0.2s;
}

.password-toggle-icon:hover {
    color: #64748b;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:focus + .input-icon {
    color: #3b82f6;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: -0.5rem 0 0.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remember-me input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #3b82f6;
    cursor: pointer;
}

.remember-me label {
    font-size: 0.85rem;
    color: #64748b;
    cursor: pointer;
    user-select: none;
}

.forgot-password {
    font-size: 0.85rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Button Styles */
.login-btn {
    width: 100%;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
}

.login-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Footer Styles */
.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.9rem;
    color: #64748b;
}

.auth-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.auth-link:hover {
    text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .left-panel {
        display: none;
    }

    .right-panel {
        width: 100%;
        margin-left: 0;
        padding: 1rem;
        min-height: 100vh;
    }

    .form-container {
        margin: 1rem 0;
        padding: 1.5rem;
    }

    .mobile-title {
        font-size: 1.5rem;
    }

    .mobile-subtitle {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .right-panel {
        padding: 0.5rem;
    }

    .form-container {
        padding: 1.25rem;
        border-radius: 16px;
    }

    .login-form {
        gap: 1rem;
    }

    .form-input {
        padding: 0.7rem 1rem 0.7rem 2.25rem;
        font-size: 0.9rem;
    }

    .input-icon {
        font-size: 0.9rem;
        left: 0.75rem;
    }

    .password-toggle-icon {
        font-size: 0.9rem;
        right: 0.75rem;
    }

    .login-btn {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}
</style> -->

<!-- <template>
    <div class="login-container">
        <div class="left-panel d-none d-lg-block">
            <div class="image-section">
                <img :src="assetPath('images/back_1.jpg')" alt="Welcome" class="welcome-image">
                <div class="welcome-overlay">
                    <div class="welcome-content">
                        <div class="brand-section">
                            <div class="brand-icon">
                                <i class="bi bi-shop"></i>
                            </div>
                            <h1 class="brand-title">ShopMaster</h1>
                        </div>

                        <h2 class="welcome-title">Welcome Back!</h2>
                        <p class="welcome-subtitle">Continue your amazing e-commerce journey with us.</p>

                        <div class="welcome-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Exclusive Deals</h4>
                                    <p>Special offers for returning customers</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Fast Shipping</h4>
                                    <p>Get your orders delivered quickly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <div class="mobile-header d-lg-none">
                    <div class="mobile-brand">
                        <i class="bi bi-shop"></i>
                        <span>ShopMaster</span>
                    </div>
                    <h2 class="mobile-title">Welcome Back</h2>
                    <p class="mobile-subtitle">Login to your account</p>
                </div>

                <div class="desktop-header d-none d-lg-block">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Login to continue to your account</p>
                </div>

                <div v-if="autoLoginLoading" class="alert alert-info">
                    <div class="alert-content">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <h5>Auto Login</h5>
                            <p>Logging in with saved credentials...</p>
                        </div>
                    </div>
                    <span class="spinner"></span>
                </div>

                <div v-if="showErrorAlert" class="alert alert-danger">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <h5>Login Failed</h5>
                            <p>{{ errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click="showErrorAlert = false"></button>
                </div>

                <div v-if="loginBlocked" class="alert alert-warning">
                    <div class="alert-content">
                        <i class="bi bi-shield-lock-fill"></i>
                        <div>
                            <h5>Login Temporarily Blocked</h5>
                            <p>Too many failed attempts. Please try again in {{ remainingTime }} seconds.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="handleLogin" class="login-form">
                    <div class="input-group">
                        <label class="input-label">Email or Username</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-input" v-model="form.login" @input="validateLogin"
                                maxlength="20" placeholder="Enter your email or username" :disabled="loginBlocked">
                        </div>
                        <div class="error-message" v-if="errors.login">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.login }}
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input :type="showPassword ? 'text' : 'password'" class="form-input" v-model="form.password"
                                @input="validatePassword" maxlength="32" placeholder="Enter your password"
                                :disabled="loginBlocked">
                            <i class="bi password-toggle-icon"
                                :class="showPassword ? 'bi-eye-fill' : 'bi-eye-slash-fill'"
                                @click="showPassword = !showPassword"></i>
                        </div>
                        <div class="error-message" v-if="errors.password">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.password }}
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" v-model="form.remember" :disabled="loginBlocked">
                            <label for="remember">Remember me</label>
                        </div>

                    </div>

                    <button type="submit" class="login-btn" :disabled="loading || autoLoginLoading || loginBlocked">
                        <span v-if="!loading">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login
                        </span>
                        <span v-else>
                            <span class="spinner"></span>
                            Logging in...
                        </span>
                    </button>

                    <div class="auth-footer">
                        <p>Don't have an account? <router-link to="/signup" class="auth-link">Sign up</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { useAdminStore } from '../../stores/adminStore';
import { useCustomerStore } from '../../stores/customerStore';

export default {
    data() {
        return {
            form: {
                login: '',
                password: '',
                remember: false
            },
            errors: {
                login: '',
                password: ''
            },
            loading: false,
            autoLoginLoading: false,
            showErrorAlert: false,
            errorMessage: '',
            showPassword: false,

            failedAttempts: 0,
            loginBlocked: false,
            blockUntil: null,
            remainingTime: 0,
            timerInterval: null
        }
    },
    created() {
        this.checkSavedCredentials();
        this.checkLoginBlockStatus();
    },
    beforeUnmount() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
    },
    methods: {
        assetPath(path) {
            return `/${path}`;
        },
        validateLogin() {
            if (!this.form.login) {
                this.errors.login = 'The email or username field is required.';
            } else if (this.form.login.length > 20) {
                this.errors.login = 'Username must be less than 20 characters.';
            } else {
                this.errors.login = '';
            }
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'The password field is required.';
            } else if (this.form.password.length > 32) {
                this.errors.password = 'Password must be less than 32 characters.';
            } else {
                this.errors.password = '';
            }
        },
        checkSavedCredentials() {
            const savedCredentials = localStorage.getItem('savedCredentials');
            if (savedCredentials) {
                try {
                    const credentials = JSON.parse(savedCredentials);
                    this.form.login = credentials.login;
                    this.form.password = credentials.password;
                    this.form.remember = true;

                    this.autoLoginLoading = true;

                    setTimeout(() => {
                        this.handleLogin();
                    }, 500);
                } catch (e) {
                    console.error('Failed to parse saved credentials', e);
                }
            }
        },
        saveCredentials() {
            if (this.form.remember) {
                const credentials = {
                    login: this.form.login,
                    password: this.form.password
                };
                localStorage.setItem('savedCredentials', JSON.stringify(credentials));
            } else {
                localStorage.removeItem('savedCredentials');
            }
        },
        validateForm() {
            this.validateLogin();
            this.validatePassword();
            return !this.errors.login && !this.errors.password;
        },
        checkLoginBlockStatus() {
            const blockData = localStorage.getItem('loginBlockData');
            if (blockData) {
                const { blockUntil } = JSON.parse(blockData);
                const now = new Date().getTime();

                if (now < blockUntil) {
                    this.loginBlocked = true;
                    this.blockUntil = blockUntil;
                    this.startTimer();
                } else {
                    localStorage.removeItem('loginBlockData');
                    this.failedAttempts = 0;
                }
            }
        },
        startTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }

            this.updateRemainingTime();

            this.timerInterval = setInterval(() => {
                this.updateRemainingTime();

                if (this.remainingTime <= 0) {
                    clearInterval(this.timerInterval);
                    this.loginBlocked = false;
                    this.failedAttempts = 0;
                    localStorage.removeItem('loginBlockData');
                }
            }, 1000);
        },
        updateRemainingTime() {
            const now = new Date().getTime();
            this.remainingTime = Math.max(0, Math.floor((this.blockUntil - now) / 1000));
        },
        blockLoginAttempts() {
            const blockDuration = 5 * 60 * 1000;
            this.blockUntil = new Date().getTime() + blockDuration;
            this.loginBlocked = true;

            localStorage.setItem('loginBlockData', JSON.stringify({
                blockUntil: this.blockUntil
            }));

            this.startTimer();
        },
        async handleLogin() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.showErrorAlert = false;

            try {
                const response = await axios.post('/login', this.form);
                console.log('Login API Response:', response.data);

                if (response.data.success) {
                    // Reset failed attempts on successful login
                    this.failedAttempts = 0;
                    localStorage.removeItem('loginBlockData');

                    this.saveCredentials();

                    const role = response.data.role;

                    if (role === 'Admin') {
                        const adminStore = useAdminStore();
                        adminStore.setPermissions(response.data.permissions);
                        adminStore.setAdminData(response.data.admin);
                        setTimeout(() => {
                            window.location.href = '/admin/home';
                        }, 10);
                    } else if (role === 'Customer') {
                        const customerStore = useCustomerStore();
                        customerStore.setCustomerData(response.data.customer);
                        setTimeout(() => {
                            window.location.href = '/customer/home';
                        }, 10);
                    } else {
                        this.errorMessage = 'Role not supported';
                        this.showErrorAlert = true;
                    }
                } else {
                    this.failedAttempts++;

                    if (this.failedAttempts >= 3) {
                        this.errorMessage = 'Too many failed attempts. Login blocked for 5 minutes.';
                        this.blockLoginAttempts();
                    } else {
                        this.errorMessage = response.data.message || 'Login failed. Please try again.';
                    }

                    this.showErrorAlert = true;
                }
            } catch (error) {
                this.failedAttempts++;

                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.errorMessage = 'Please fix the validation errors';
                    } else if (error.response.status === 401) {
                        if (this.failedAttempts >= 3) {
                            this.errorMessage = 'Too many failed attempts. Login blocked for 5 minutes.';
                            this.blockLoginAttempts();
                        } else {
                            this.errorMessage = 'Invalid credentials. Please try again.';
                        }
                    } else if (error.response.status === 403) {
                        this.errorMessage = 'You are not authorized to access this system.';
                    } else {
                        this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
                    }
                } else {
                    this.errorMessage = 'Network error. Please check your connection and try again.';
                }

                this.showErrorAlert = true;
                console.error('Login error:', error);
            } finally {
                this.loading = false;
                this.autoLoginLoading = false;
            }
        }
    }
}
</script>

<style scoped>
.login-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.left-panel {
    width: 50%;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    overflow: hidden;
}

.image-section {
    width: 100%;
    height: 100%;
    position: relative;
}

.welcome-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.welcome-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.welcome-content {
    max-width: 420px;
    text-align: center;
    color: white;
}

.brand-section {
    margin-bottom: 2rem;
}

.brand-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.brand-icon i {
    font-size: 1.8rem;
    color: white;
}

.brand-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.welcome-subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.5;
}

.welcome-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: left;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.1rem;
    color: white;
}

.feature-content h4 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.2rem 0;
}

.feature-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.right-panel {
    width: 50%;
    margin-left: 50%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.form-container {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.mobile-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1e293b;
}

.mobile-brand i {
    font-size: 1.8rem;
    color: #3b82f6;
}

.mobile-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.mobile-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.mobile-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.desktop-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.alert {
    border: none;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.alert-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.alert-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    flex-grow: 1;
}

.alert-content i {
    font-size: 1.5rem;
}

.alert-content h5 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.alert-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1rem;
    padding: 0;
    cursor: pointer;
    margin-left: 0.5rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.input-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    font-size: 1rem;
}

.password-toggle-icon {
    position: absolute;
    right: 1rem;
    color: #9ca3af;
    font-size: 1.1rem;
    cursor: pointer;
    transition: color 0.2s;
}

.password-toggle-icon:hover {
    color: #64748b;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:focus+.input-icon {
    color: #3b82f6;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: -0.5rem 0 0.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remember-me input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #3b82f6;
    cursor: pointer;
}

.remember-me label {
    font-size: 0.85rem;
    color: #64748b;
    cursor: pointer;
    user-select: none;
}

.forgot-password {
    font-size: 0.85rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

.login-btn {
    width: 100%;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
}

.login-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.9rem;
    color: #64748b;
}

.auth-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.auth-link:hover {
    text-decoration: underline;
}

@media (max-width: 991.98px) {
    .left-panel {
        display: none;
    }

    .right-panel {
        width: 100%;
        margin-left: 0;
        padding: 1rem;
        min-height: 100vh;
    }

    .form-container {
        margin: 1rem 0;
        padding: 1.5rem;
    }

    .mobile-title {
        font-size: 1.5rem;
    }

    .mobile-subtitle {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .right-panel {
        padding: 0.5rem;
    }

    .form-container {
        padding: 1.25rem;
        border-radius: 16px;
    }

    .login-form {
        gap: 1rem;
    }

    .form-input {
        padding: 0.7rem 1rem 0.7rem 2.25rem;
        font-size: 0.9rem;
    }

    .input-icon {
        font-size: 0.9rem;
        left: 0.75rem;
    }

    .password-toggle-icon {
        font-size: 0.9rem;
        right: 0.75rem;
    }

    .login-btn {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}
</style> -->


<template>
    <div class="login-container">
        <div class="left-panel d-none d-lg-block">
            <div class="image-section">
                <img :src="assetPath('images/back_1.jpg')" alt="Welcome" class="welcome-image">
                <div class="welcome-overlay">
                    <div class="welcome-content">
                        <div class="brand-section">
                            <div class="brand-icon">
                                <i class="bi bi-shop"></i>
                            </div>
                            <h1 class="brand-title">ShopMaster</h1>
                        </div>

                        <h2 class="welcome-title">Welcome Back!</h2>
                        <p class="welcome-subtitle">Continue your amazing e-commerce journey with us.</p>

                        <div class="welcome-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Exclusive Deals</h4>
                                    <p>Special offers for returning customers</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Fast Shipping</h4>
                                    <p>Get your orders delivered quickly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <div class="mobile-header d-lg-none">
                    <div class="mobile-brand">
                        <i class="bi bi-shop"></i>
                        <span>ShopMaster</span>
                    </div>
                    <h2 class="mobile-title">Welcome Back</h2>
                    <p class="mobile-subtitle">Login to your account</p>
                </div>

                <div class="desktop-header d-none d-lg-block">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Login to continue to your account</p>
                </div>

                <div v-if="autoLoginLoading" class="alert alert-info">
                    <div class="alert-content">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <h5>Auto Login</h5>
                            <p>Logging in with saved credentials...</p>
                        </div>
                    </div>
                    <span class="spinner"></span>
                </div>

                <div v-if="showErrorAlert" class="alert alert-danger">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <h5>Login Failed</h5>
                            <p>{{ errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click="showErrorAlert = false"></button>
                </div>

                <div v-if="loginBlocked" class="alert alert-warning">
                    <div class="alert-content">
                        <i class="bi bi-shield-lock-fill"></i>
                        <div>
                            <h5>Login Temporarily Blocked</h5>
                            <p>Too many failed attempts. Please try again in {{ remainingTime }} seconds.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="handleLogin" class="login-form">
                    <div class="input-group">
                        <label class="input-label">Email or Username</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-input" v-model="form.login" @input="validateLogin"
                                maxlength="20" placeholder="Enter your email or username" :disabled="loginBlocked">
                        </div>
                        <div class="error-message" v-if="errors.login">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.login }}
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input :type="showPassword ? 'text' : 'password'" class="form-input" v-model="form.password"
                                @input="validatePassword" maxlength="32" placeholder="Enter your password"
                                :disabled="loginBlocked">
                            <i class="bi password-toggle-icon"
                                :class="showPassword ? 'bi-eye-fill' : 'bi-eye-slash-fill'"
                                @click="showPassword = !showPassword"></i>
                        </div>
                        <div class="error-message" v-if="errors.password">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.password }}
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" v-model="form.remember" :disabled="loginBlocked">
                            <label for="remember">Remember me</label>
                        </div>
                        <!-- <router-link v-if="!form.remember" to="/forgot-password" class="forgot-password">Forgot password?</router-link> -->
                    </div>

                    <button type="submit" class="login-btn" :disabled="loading || autoLoginLoading || loginBlocked">
                        <span v-if="!loading">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login
                        </span>
                        <span v-else>
                            <span class="spinner"></span>
                            Logging in...
                        </span>
                    </button>

                    <div class="auth-footer">
                        <p>Don't have an account? <router-link to="/signup" class="auth-link">Sign up</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { useAdminStore } from '../../stores/adminStore';
import { useCustomerStore } from '../../stores/customerStore';
import { useCartStore } from '../../stores/cartStore';
export default {
    data() {
        return {
            form: {
                login: '',
                password: '',
                remember: false
            },
            errors: {
                login: '',
                password: ''
            },
            loading: false,
            autoLoginLoading: false,
            showErrorAlert: false,
            errorMessage: '',
            showPassword: false,
           
            failedAttempts: 0,
            loginBlocked: false,
            blockUntil: null,
            remainingTime: 0,
            timerInterval: null
        }
    },
    created() {
        this.checkSavedCredentials();
        this.checkLoginBlockStatus();
    },
    beforeUnmount() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
    },
    methods: {
        assetPath(path) {
            return `/${path}`;
        },
        validateLogin() {
            if (!this.form.login) {
                this.errors.login = 'The email or username field is required.';
            } else if (this.form.login.length > 20) {
                this.errors.login = 'Username must be less than 20 characters.';
            } else {
                this.errors.login = '';
            }
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'The password field is required.';
            } else if (this.form.password.length > 32) {
                this.errors.password = 'Password must be less than 32 characters.';
            } else {
                this.errors.password = '';
            }
        },
        checkSavedCredentials() {
            const savedCredentials = localStorage.getItem('savedCredentials');
            if (savedCredentials) {
                try {
                    const credentials = JSON.parse(savedCredentials);
                    this.form.login = credentials.login;
                    this.form.password = credentials.password;
                    this.form.remember = true;

                    this.autoLoginLoading = true;

                    setTimeout(() => {
                        this.handleLogin();
                    }, 500);
                } catch (e) {
                    console.error('Failed to parse saved credentials', e);
                }
            }
        },
        saveCredentials() {
            if (this.form.remember) {
                const credentials = {
                    login: this.form.login,
                    password: this.form.password
                };
                localStorage.setItem('savedCredentials', JSON.stringify(credentials));
            } else {
                localStorage.removeItem('savedCredentials');
            }
        },
        validateForm() {
            this.validateLogin();
            this.validatePassword();
            return !this.errors.login && !this.errors.password;
        },
        checkLoginBlockStatus() {
            const blockData = localStorage.getItem('loginBlockData');
            if (blockData) {
                const { blockUntil } = JSON.parse(blockData);
                const now = new Date().getTime();

                if (now < blockUntil) {
                    this.loginBlocked = true;
                    this.blockUntil = blockUntil;
                    this.startTimer();
                } else {
                    localStorage.removeItem('loginBlockData');
                    this.failedAttempts = 0;
                }
            }
        },
        startTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }

            this.updateRemainingTime();

            this.timerInterval = setInterval(() => {
                this.updateRemainingTime();

                if (this.remainingTime <= 0) {
                    clearInterval(this.timerInterval);
                    this.loginBlocked = false;
                    this.failedAttempts = 0;
                    localStorage.removeItem('loginBlockData');
                }
            }, 1000);
        },
        updateRemainingTime() {
            const now = new Date().getTime();
            this.remainingTime = Math.max(0, Math.floor((this.blockUntil - now) / 1000));
        },
        blockLoginAttempts() {
            const blockDuration = 5 * 60 * 1000; // 5 minutes in milliseconds
            this.blockUntil = new Date().getTime() + blockDuration;
            this.loginBlocked = true;

            localStorage.setItem('loginBlockData', JSON.stringify({
                blockUntil: this.blockUntil
            }));

            this.startTimer();
        },
        async handleLogin() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.showErrorAlert = false;

            try {
                const response = await axios.post('/login', this.form);
                console.log('Login API Response:', response.data);

                if (response.data.success) {
                    // Reset failed attempts on successful login
                    this.failedAttempts = 0;
                    localStorage.removeItem('loginBlockData');

                    this.saveCredentials();

                    const role = response.data.role;

                    if (role === 'Admin') {
                        this.form.login = '';
                        this.errors.login = '';
                        const adminStore = useAdminStore();
                       
                        adminStore.setPermissions(response.data.permissions);
                        adminStore.setAdminData(response.data.admin);
                        setTimeout(() => {
                            window.location.href = '/admin/home';
                        }, 10);
                    } else if (role === 'Customer') {
                        this.form.login = '';
                        this.errors.login = '';
                        const customerStore = useCustomerStore();
                         const cartStore = useCartStore();
                        // cartStore.setCustomerId(response.data.customer.id);
                        // cartStore.checkUserCart(response.data.customer.id);
                        customerStore.setCustomerData(response.data.customer);
                        setTimeout(() => {
                            window.location.href = '/customer/home';
                        }, 10);
                    } else {
                        this.errorMessage = 'Role not supported';
                        this.showErrorAlert = true;
                    }
                } else {
                    this.failedAttempts++;

                    if (this.failedAttempts >= 3) {
                        this.errorMessage = 'Too many failed attempts. Login blocked for 5 minutes.';
                        this.blockLoginAttempts();
                    } else {
                        this.errorMessage = response.data.message || 'Login failed. Please try again.';
                    }

                    this.showErrorAlert = true;
                }
            } catch (error) {
                this.failedAttempts++;

                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.errorMessage = 'Please fix the validation errors';
                    } else if (error.response.status === 401) {
                        if (this.failedAttempts >= 3) {
                            this.errorMessage = 'Too many failed attempts. Login blocked for 5 minutes.';
                            this.blockLoginAttempts();
                        } else {
                            this.errorMessage = 'Invalid credentials. Please try again.';
                        }
                    } else if (error.response.status === 403) {
                        this.errorMessage = 'You are not authorized to access this system.';
                    } else {
                        this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
                    }
                } else {
                    this.errorMessage = 'Network error. Please check your connection and try again.';
                }

                this.showErrorAlert = true;
                console.error('Login error:', error);
            } finally {
                this.loading = false;
                this.autoLoginLoading = false;
            }
        }
    }
}
</script>

<style scoped>
.login-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.left-panel {
    width: 50%;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    overflow: hidden;
}

.image-section {
    width: 100%;
    height: 100%;
    position: relative;
}

.welcome-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.welcome-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.welcome-content {
    max-width: 420px;
    text-align: center;
    color: white;
}

.brand-section {
    margin-bottom: 2rem;
}

.brand-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.brand-icon i {
    font-size: 1.8rem;
    color: white;
}

.brand-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.welcome-subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.5;
}

.welcome-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: left;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.1rem;
    color: white;
}

.feature-content h4 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.2rem 0;
}

.feature-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

/* Right Panel Styles */
.right-panel {
    width: 50%;
    margin-left: 50%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.form-container {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.mobile-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1e293b;
}

.mobile-brand i {
    font-size: 1.8rem;
    color: #3b82f6;
}

.mobile-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.mobile-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.mobile-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.desktop-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.alert {
    border: none;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.alert-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.alert-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    flex-grow: 1;
}

.alert-content i {
    font-size: 1.5rem;
}

.alert-content h5 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.alert-content p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1rem;
    padding: 0;
    cursor: pointer;
    margin-left: 0.5rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.input-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    font-size: 1rem;
}

.password-toggle-icon {
    position: absolute;
    right: 1rem;
    color: #9ca3af;
    font-size: 1.1rem;
    cursor: pointer;
    transition: color 0.2s;
}

.password-toggle-icon:hover {
    color: #64748b;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:focus+.input-icon {
    color: #3b82f6;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: -0.5rem 0 0.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remember-me input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #3b82f6;
    cursor: pointer;
}

.remember-me label {
    font-size: 0.85rem;
    color: #64748b;
    cursor: pointer;
    user-select: none;
}

.forgot-password {
    font-size: 0.85rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Button Styles */
.login-btn {
    width: 100%;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
}

.login-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Footer Styles */
.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.9rem;
    color: #64748b;
}

.auth-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.auth-link:hover {
    text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .left-panel {
        display: none;
    }

    .right-panel {
        width: 100%;
        margin-left: 0;
        padding: 1rem;
        min-height: 100vh;
    }

    .form-container {
        margin: 1rem 0;
        padding: 1.5rem;
    }

    .mobile-title {
        font-size: 1.5rem;
    }

    .mobile-subtitle {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .right-panel {
        padding: 0.5rem;
    }

    .form-container {
        padding: 1.25rem;
        border-radius: 16px;
    }

    .login-form {
        gap: 1rem;
    }

    .form-input {
        padding: 0.7rem 1rem 0.7rem 2.25rem;
        font-size: 0.9rem;
    }

    .input-icon {
        font-size: 0.9rem;
        left: 0.75rem;
    }

    .password-toggle-icon {
        font-size: 0.9rem;
        right: 0.75rem;
    }

    .login-btn {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}
</style>