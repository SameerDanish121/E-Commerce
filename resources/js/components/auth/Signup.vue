<!-- 
<template>
    <div class="signup-container">
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
                        <p class="welcome-subtitle">Join thousands of satisfied customers in our amazing e-commerce journey.</p>
                        
                        <div class="welcome-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Lightning Fast</h4>
                                    <p>Quick and secure checkout</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Free Delivery</h4>
                                    <p>On orders over $50</p>
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
                    <h2 class="mobile-title">Create Account</h2>
                    <p class="mobile-subtitle">Join our community today and start shopping!</p>
                </div>

                <div class="desktop-header d-none d-lg-block">
                    <h2 class="form-title">Create Your Account</h2>
                    <p class="form-subtitle">Fill in your details to get started with us</p>
                </div>

                <div v-if="showSuccessAlert" class="alert alert-success alert-dismissible custom-alert" role="alert">
                    <div class="alert-content">
                        <div class="alert-icon-wrapper">
                            <i class="bi bi-check-circle-fill alert-icon"></i>
                        </div>
                        <div class="alert-text">
                            <h5 class="alert-title">Account Created!</h5>
                            <p class="alert-message">Welcome to our community!</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click="showSuccessAlert = false"></button>
                </div>

                <form @submit.prevent="handleSubmit" class="modern-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Username</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-person input-icon"></i>
                                    <input 
                                        type="text" 
                                        class="modern-input" 
                                        id="username" 
                                        v-model="form.username"
                                        required
                                        placeholder="Username"
                                    >
                                </div>
                                <div class="error-message" v-if="errors.username">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.username[0] }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Full Name</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-person-badge input-icon"></i>
                                    <input 
                                        type="text" 
                                        class="modern-input" 
                                        id="name" 
                                        v-model="form.name"
                                        required
                                        placeholder="Full Name"
                                    >
                                </div>
                                <div class="error-message" v-if="errors.name">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.name[0] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Email Address</label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon"></i>
                            <input 
                                type="email" 
                                class="modern-input" 
                                id="email" 
                                v-model="form.email"
                                required
                                placeholder="Email Address"
                            >
                        </div>
                        <div class="error-message" v-if="errors.email">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.email[0] }}
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Password</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-lock input-icon"></i>
                                    <input 
                                        type="password" 
                                        class="modern-input" 
                                        id="password" 
                                        v-model="form.password"
                                        required
                                        placeholder="Password"
                                    >
                                </div>
                                <div class="error-message" v-if="errors.password">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.password[0] }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Confirm Password</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-shield-lock input-icon"></i>
                                    <input 
                                        type="password" 
                                        class="modern-input" 
                                        id="password_confirmation" 
                                        v-model="form.password_confirmation"
                                        required
                                        placeholder="Confirm Password"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Phone Number</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-telephone input-icon"></i>
                                    <input 
                                        type="tel" 
                                        class="modern-input" 
                                        id="phone_no" 
                                        v-model="form.phone_no"
                                        placeholder="Phone Number (Optional)"
                                    >
                                </div>
                                <div class="error-message" v-if="errors.phone_no">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.phone_no[0] }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Gender</label>
                                <div class="input-wrapper">
                                    <i class="bi bi-gender-ambiguous input-icon"></i>
                                    <select class="modern-input" id="gender" v-model="form.gender">
                                        <option value=""></option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="error-message" v-if="errors.gender">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.gender[0] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Address</label>
                        <div class="input-wrapper">
                            <i class="bi bi-geo-alt input-icon textarea-icon"></i>
                            <textarea 
                                class="modern-textarea" 
                                id="address" 
                                v-model="form.address"
                                placeholder="Address (Optional)"
                                style="height: 100px"
                            ></textarea>
                        </div>
                        <div class="error-message" v-if="errors.address">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.address[0] }}
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Date of Birth</label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar input-icon"></i>
                            <input 
                                type="date" 
                                class="modern-input" 
                                id="dob" 
                                v-model="form.dob"
                            >
                        </div>
                        <div class="error-message" v-if="errors.dob">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.dob[0] }}
                        </div>
                    </div>
                    
                    <button type="submit" class="modern-btn" :disabled="loading">
                        <span v-if="!loading" class="btn-content">
                            <i class="bi bi-person-plus-fill"></i>
                            Create Account
                        </span>
                        <span v-else class="btn-content">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Creating Account...
                        </span>
                    </button>
                    
                    <div class="form-footer">
                        <p class="login-link">
                            Already have an account? 
                            <router-link to="/" class="link-primary">Sign In</router-link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            form: {
                username: '',
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                phone_no: '',
                address: '',
                dob: '',
                gender: '',
                profile_pic: ''
            },
            errors: {},
            loading: false,
            showSuccessAlert: false
        }
    },
    methods: {
        assetPath(path) {
            return `/${path}`;
        },
        async handleSubmit() {
            this.loading = true;
            this.errors = {};
            this.showSuccessAlert = false;

            try {
                const response = await axios.post('/signup', this.form);
                this.showSuccessAlert = true;
                setTimeout(() => {
                    this.$router.push('/');
                }, 3000);
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    console.error("Signup error:", error);
                    alert('An error occurred during signup. Please try again.');
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');

.signup-container {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
    width: 100%;
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

.custom-alert {
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    margin-bottom: 1.5rem;
    padding: 1rem;
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-icon {
    font-size: 1.5rem;
}

.alert-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.alert-message {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.custom-alert .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    position: absolute;
    right: 1rem;
    top: 1rem;
}

/* Modern Form Styling */
.modern-form {
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

.textarea-icon {
    top: 1rem;
    align-self: flex-start;
}

.modern-input,
.modern-textarea {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.modern-input:focus,
.modern-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modern-input:focus + .input-icon,
.modern-textarea:focus + .input-icon {
    color: #3b82f6;
}

.modern-textarea {
    resize: vertical;
    min-height: 80px;
    padding-top: 1rem;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.modern-btn {
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
}

.modern-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.modern-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.form-footer {
    text-align: center;
    margin-top: 1.5rem;
}

.login-link {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.link-primary {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.link-primary:hover {
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
    
    .modern-form {
        gap: 1rem;
    }
    
    .modern-input,
    .modern-textarea {
        padding: 0.7rem 1rem 0.7rem 2.25rem;
        font-size: 0.9rem;
    }
    
    .input-icon {
        font-size: 0.9rem;
        left: 0.75rem;
    }
    
    .modern-btn {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}

/* Additional styles for select element */
.modern-input[type="date"],
.modern-input[type="tel"],
select.modern-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

select.modern-input {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}
</style> -->




<template>
    <div class="signup-container">
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
                        <p class="welcome-subtitle">Join thousands of satisfied customers in our amazing e-commerce
                            journey.</p>

                        <div class="welcome-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Lightning Fast</h4>
                                    <p>Quick and secure checkout</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Free Delivery</h4>
                                    <p>On orders over $50</p>
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
                    <h2 class="mobile-title">Create Account</h2>
                    <p class="mobile-subtitle">Join our community today and start shopping!</p>
                </div>

                <div class="desktop-header d-none d-lg-block">
                    <h2 class="form-title">Create Your Account</h2>
                    <p class="form-subtitle">Fill in your details to get started with us</p>
                </div>

                <div v-if="showSuccessAlert" class="alert alert-success alert-dismissible custom-alert" role="alert">
                    <div class="alert-content">
                        <div class="alert-icon-wrapper">
                            <i class="bi bi-check-circle-fill alert-icon"></i>
                        </div>
                        <div class="alert-text">
                            <h5 class="alert-title">Account Created!</h5>
                            <p class="alert-message">Welcome to our community!</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click="showSuccessAlert = false"></button>
                </div>

                <form @submit.prevent="handleSubmit" class="modern-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Username <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-person input-icon" :class="{ 'error-icon': errors.username }"></i>
                                    <input type="text" class="modern-input" :class="{ 'error-input': errors.username }"
                                        id="username" v-model="form.username" @input="validateUsername" maxlength="20"
                                        placeholder="Enter username">
                                </div>
                                <div class="error-message" v-if="errors.username">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.username }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Full Name <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-person-badge input-icon" :class="{ 'error-icon': errors.name }"></i>
                                    <input type="text" class="modern-input" :class="{ 'error-input': errors.name }"
                                        id="name" v-model="form.name" @input="validateName"
                                        placeholder="Enter your full name">
                                </div>
                                <div class="error-message" v-if="errors.name">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon" :class="{ 'error-icon': errors.email }"></i>
                            <input type="email" class="modern-input" :class="{ 'error-input': errors.email }" id="email"
                                v-model="form.email" @input="validateEmail" placeholder="Enter your email">
                        </div>
                        <div class="error-message" v-if="errors.email">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.email }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Password <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-lock input-icon" :class="{ 'error-icon': errors.password }"></i>
                                    <input type="password" class="modern-input"
                                        :class="{ 'error-input': errors.password }" id="password"
                                        v-model="form.password" @input="validatePassword" maxlength="32"
                                        placeholder="Enter password">
                                </div>
                                <div class="error-message" v-if="errors.password">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.password }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Confirm Password <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-shield-lock input-icon"
                                        :class="{ 'error-icon': errors.password_confirmation }"></i>
                                    <input type="password" class="modern-input"
                                        :class="{ 'error-input': errors.password_confirmation }"
                                        id="password_confirmation" v-model="form.password_confirmation"
                                        @input="validatePasswordConfirmation" maxlength="32"
                                        placeholder="Confirm password">
                                </div>
                                <div class="error-message" v-if="errors.password_confirmation">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.password_confirmation }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Phone Number <span class="optional">(Optional)</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-telephone input-icon"
                                        :class="{ 'error-icon': errors.phone_no }"></i>
                                    <input type="tel" class="modern-input" :class="{ 'error-input': errors.phone_no }"
                                        id="phone_no" v-model="form.phone_no" @input="validatePhone"
                                        placeholder="Enter phone number" maxlength="15">
                                </div>
                                <div class="error-message" v-if="errors.phone_no">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.phone_no }}
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-label">Gender <span class="optional">(Optional)</span></label>
                                <div class="input-wrapper">
                                    <i class="bi bi-gender-ambiguous input-icon"
                                        :class="{ 'error-icon': errors.gender }"></i>
                                    <select class="modern-input" :class="{ 'error-input': errors.gender }" id="gender"
                                        v-model="form.gender" @change="validateGender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="error-message" v-if="errors.gender">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ errors.gender }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="input-group">
                        <label class="input-label">Address <span class="optional">(Optional)</span></label>
                        <div class="input-wrapper">
                            <i class="bi bi-geo-alt input-icon textarea-icon"
                                :class="{ 'error-icon': errors.address }"></i>
                            <textarea class="modern-textarea" :class="{ 'error-input': errors.address }" id="address"
                                v-model="form.address" @input="validateAddress" placeholder="Enter your address"
                                style="height: 100px"></textarea>
                        </div>
                        <div class="error-message" v-if="errors.address">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.address }}
                        </div>
                    </div> -->
                    <div class="input-group">
                        <label class="input-label">Address <span class="optional">(Optional)</span></label>
                        <div class="input-wrapper">
                            <i class="bi bi-geo-alt input-icon textarea-icon"
                                :class="{ 'error-icon': errors.address }"></i>

                            <textarea class="modern-textarea" :class="{ 'error-input': errors.address }" id="address"
                                v-model="form.address" @input="validateAddress" placeholder="Enter your address"
                                maxlength="100" style="height: 100px; resize: none;"></textarea>
                        </div>

                        <div class="error-message" v-if="errors.address">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.address }}
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Date of Birth <span class="optional">(Optional)</span></label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar input-icon" :class="{ 'error-icon': errors.dob }"></i>
                            <input type="date" class="modern-input" :class="{ 'error-input': errors.dob }" id="dob"
                                v-model="form.dob" @change="validateDob" :max="maxDobDate">
                        </div>
                        <div class="error-message" v-if="errors.dob">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ errors.dob }}
                        </div>
                    </div>

                    <button type="submit" class="modern-btn" :disabled="loading || !isFormValid">
                        <span v-if="!loading" class="btn-content">
                            <i class="bi bi-person-plus-fill"></i>
                            Create Account
                        </span>
                        <span v-else class="btn-content">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Creating Account...
                        </span>
                    </button>

                    <div class="form-footer">
                        <p class="login-link">
                            Already have an account?
                            <router-link to="/" class="link-primary">Sign In</router-link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        const today = new Date();
        const maxDobDate = new Date(today.getFullYear() - 10, today.getMonth(), today.getDate())
            .toISOString()
            .split('T')[0];

        return {
            form: {
                username: '',
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                phone_no: '',
                address: '',
                dob: '',
                gender: '',
                profile_pic: ''
            },
            errors: {
                username: '',
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                phone_no: '',
                address: '',
                dob: '',
                gender: ''
            },
            loading: false,
            showSuccessAlert: false,
            maxDobDate
        }
    },
    computed: {
        isFormValid() {
            return (
                this.form.username &&
                this.form.name &&
                this.form.email &&
                this.form.password &&
                this.form.password_confirmation &&
                !this.errors.username &&
                !this.errors.name &&
                !this.errors.email &&
                !this.errors.password &&
                !this.errors.password_confirmation
            );
        }
    },
    methods: {
        assetPath(path) {
            return `/${path}`;
        },
        validateUsername() {
            if (!this.form.username) {
                this.errors.username = 'Username is required';
                return;
            }

            if (this.form.username.length > 20) {
                this.errors.username = 'Username must be less than 20 characters';
                return;
            }

            // Alphanumeric with underscores and dots, no spaces
            const usernameRegex = /^[a-zA-Z0-9_.]+$/;
            if (!usernameRegex.test(this.form.username)) {
                this.errors.username = 'Username can only contain letters, numbers, underscores and dots';
                return;
            }

            this.errors.username = '';
        },
        validateName() {
            if (!this.form.name) {
                this.errors.name = 'Full name is required';
                return;
            }

            // Name with spaces, apostrophes, and hyphens allowed
            const nameRegex = /^[a-zA-Z' -]+$/;
            if (!nameRegex.test(this.form.name)) {
                this.errors.name = 'Please enter a valid name';
                return;
            }

            this.errors.name = '';
        },
        validateEmail() {
            if (!this.form.email) {
                this.errors.email = 'Email is required';
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.form.email)) {
                this.errors.email = 'Please enter a valid email address';
                return;
            }

            this.errors.email = '';
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'Password is required';
                return;
            }

            if (this.form.password.length < 6) {
                this.errors.password = 'Password must be at least 6 characters';
                return;
            }

            if (this.form.password.length > 32) {
                this.errors.password = 'Password must be less than 32 characters';
                return;
            }

            // At least one uppercase, one lowercase, one number
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
            if (!passwordRegex.test(this.form.password)) {
                this.errors.password = 'Password must contain at least one uppercase letter, one lowercase letter and one number';
                return;
            }

            // Validate password confirmation if it exists
            if (this.form.password_confirmation) {
                this.validatePasswordConfirmation();
            }

            this.errors.password = '';
        },
        validatePasswordConfirmation() {
            if (!this.form.password_confirmation) {
                this.errors.password_confirmation = 'Please confirm your password';
                return;
            }

            if (this.form.password !== this.form.password_confirmation) {
                this.errors.password_confirmation = 'Passwords do not match';
                return;
            }

            this.errors.password_confirmation = '';
        },
        validatePhone() {
            const phone = this.form.phone_no?.trim();

            // If empty, clear the error
            if (!phone) {
                this.errors.phone_no = '';
                return;
            }

            // Remove all non-digit characters except '+'
            const cleaned = phone.replace(/[^\d+]/g, '');

            // Pakistani format: must be exactly 11 digits and start with '03'
            const isPakistani = /^03\d{9}$/.test(cleaned);

            // International format: must start with '+' and be 11-15 digits total
            const isInternational = /^\+?\d{10,15}$/.test(cleaned);

            if (!isPakistani && !isInternational) {
                this.errors.phone_no = 'Please enter a valid Pakistani or international phone number';
                return;
            }

            this.errors.phone_no = '';
        },

        validateAddress() {
            // No validation needed as it's optional
            this.errors.address = '';
        },
        validateDob() {
            if (!this.form.dob) {
                this.errors.dob = '';
                return;
            }

            const dobDate = new Date(this.form.dob);
            const today = new Date();
            const minDobDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());

            if (dobDate > new Date(this.maxDobDate)) {
                this.errors.dob = 'You must be at least 10 years old';
                return;
            }

            if (dobDate < minDobDate) {
                this.errors.dob = 'Please enter a valid date of birth';
                return;
            }

            this.errors.dob = '';
        },
        validateGender() {
            if (!this.form.gender) {
                this.errors.gender = '';
                return;
            }

            if (!['Male', 'Female', 'Other'].includes(this.form.gender)) {
                this.errors.gender = 'Please select a valid gender';
                return;
            }

            this.errors.gender = '';
        },
        validateForm() {
            this.validateUsername();
            this.validateName();
            this.validateEmail();
            this.validatePassword();
            this.validatePasswordConfirmation();
            this.validatePhone();
            this.validateDob();
            this.validateGender();
            this.validateAddress();

            return Object.values(this.errors).every(error => !error);
        },
        async handleSubmit() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.showSuccessAlert = false;

            try {
                const response = await axios.post('/signup', this.form);
                this.showSuccessAlert = true;
                setTimeout(() => {
                    this.$router.push('/');
                }, 3000);
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Handle server-side validation errors
                    const serverErrors = error.response.data.errors || {};
                    for (const field in serverErrors) {
                        this.errors[field] = serverErrors[field][0];
                    }
                } else {
                    console.error("Signup error:", error);
                    this.errors.general = 'An error occurred during signup. Please try again.';
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');

.signup-container {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
    width: 100%;
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

.custom-alert {
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    margin-bottom: 1.5rem;
    padding: 1rem;
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-icon {
    font-size: 1.5rem;
}

.alert-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.alert-message {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.9;
}

.custom-alert .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    position: absolute;
    right: 1rem;
    top: 1rem;
}

/* Modern Form Styling */
.modern-form {
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

.required {
    color: #ef4444;
    margin-left: 2px;
}

.optional {
    color: #64748b;
    font-size: 0.75rem;
    margin-left: 4px;
    font-weight: normal;
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
    transition: color 0.2s;
}

.error-icon {
    color: #ef4444 !important;
}

.textarea-icon {
    top: 1rem;
    align-self: flex-start;
}

.modern-input,
.modern-textarea {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.modern-input:focus,
.modern-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modern-input:focus+.input-icon,
.modern-textarea:focus+.input-icon {
    color: #3b82f6;
}

.error-input {
    border-color: #ef4444 !important;
}

.error-input:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.modern-textarea {
    resize: vertical;
    min-height: 80px;
    padding-top: 1rem;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.modern-btn {
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
}

.modern-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.modern-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background: #e5e7eb;
    color: #9ca3af;
}

.btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.form-footer {
    text-align: center;
    margin-top: 1.5rem;
}

.login-link {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.link-primary {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.link-primary:hover {
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

    .modern-form {
        gap: 1rem;
    }

    .modern-input,
    .modern-textarea {
        padding: 0.7rem 1rem 0.7rem 2.25rem;
        font-size: 0.9rem;
    }

    .input-icon {
        font-size: 0.9rem;
        left: 0.75rem;
    }

    .modern-btn {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}

/* Additional styles for select element */
.modern-input[type="date"],
.modern-input[type="tel"],
select.modern-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

select.modern-input {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}

.spinner-border {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    vertical-align: text-bottom;
    border: 0.2em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}
</style>