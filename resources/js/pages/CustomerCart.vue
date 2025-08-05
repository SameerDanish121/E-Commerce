<template>
  <div class="cart-view">
    <section class="cart-hero">
      <div class="container">
        <div
          class="cart-hero-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
          <div>
            <h1 class="mb-1">Your Shopping Cart</h1>
            <p class="lead mb-0">
              {{ cartStore.totalItems }} {{ cartStore.totalItems === 1 ? 'item' : 'items' }} in your cart
            </p>
          </div>
          <router-link to="/home" class="btn btn-outline-primary mt-3 mt-md-0">
            <i class="bi bi-arrow-left me-2"></i> Continue Shopping
          </router-link>
        </div>
      </div>
    </section>

    <section class="cart-content py-4">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div v-if="cartStore.items.length === 0" class="empty-cart text-center py-5">
              <div class="empty-icon mb-4">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: #6c757d;"></i>
              </div>
              <h3>Your cart is empty</h3>
              <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet</p>
              <router-link to="/home" class="btn btn-primary px-4">
                <i class="bi bi-bag me-2"></i> Start Shopping
              </router-link>
            </div>

            <div v-else class="cart-items">
              <div v-for="item in cartStore.items" :key="item.id"
                class="cart-item mb-3 p-3 bg-white rounded-3 shadow-sm">
                <div class="row align-items-center">
                  <div class="col-3 col-md-2">
                    <img :src="item.image_url" :alt="item.name" class="img-fluid rounded">
                  </div>
                  <div class="col-5 col-md-4">
                    <h5 class="mb-1">{{ item.name }}</h5>
                    <p class="text-muted small mb-0">{{ item.category }}</p>
                    <p class="mb-0 text-primary fw-bold">Rs {{ item.price.toLocaleString('en-IN') }}</p>
                    <p class="small text-muted mb-0">Available: {{ item.stock }}</p>
                  </div>
                  <!-- <div class="col-4 col-md-3">
                    <div class="input-group quantity-control">
                      <button class="btn btn-outline-secondary" type="button" @click="decreaseQuantity(item)"
                        :disabled="item.quantity <= 1">
                        <i class="bi bi-dash-lg"></i>
                      </button>
                      <input type="text" class="form-control text-center" :value="item.quantity" readonly>
                      <button class="btn btn-outline-secondary" type="button" @click="increaseQuantity(item)"
                        :disabled="item.quantity >= item.stock">
                        <i class="bi bi-plus-lg"></i>
                      </button>
                    </div>
                  </div> -->
                  <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="input-group quantity-control flex-nowrap">
                      <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button"
                        @click="decreaseQuantity(item)" :disabled="item.quantity <= 1">
                        <i class="bi bi-dash-lg"></i>
                      </button>
                      <input type="text" class="form-control text-center quantity-input" :value="item.quantity"
                        readonly>
                      <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button"
                        @click="increaseQuantity(item)" :disabled="item.quantity >= item.stock">
                        <i class="bi bi-plus-lg"></i>
                      </button>
                    </div>
                  </div>

                  <div class="col-12 col-md-3 mt-3 mt-md-0 text-md-end">( Quantity x Price )
                    <p class="mb-1 fw-bold">Rs {{ (item.price * item.quantity).toLocaleString('en-IN') }}</p>
                    <button class="btn btn-link text-danger p-0 small" @click="removeItem(item.id)">
                      <i class="bi bi-trash me-1"></i> Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="order-summary p-4 bg-white rounded-3 shadow-sm">
              <h3 class="mb-4">Order Summary</h3>

              <div class="d-flex justify-content-between mb-3">
                <span>Subtotal ({{ cartStore.totalItems }} items)</span>
                <span class="fw-bold">Rs {{ cartStore.subtotal.toLocaleString('en-IN') }}</span>
              </div>

              <div class="d-flex justify-content-between mb-3">
                <span>Shipping</span>
                <span class="text-success">FREE</span>
              </div>

              <hr class="my-3">

              <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold fs-5">Total</span>
                <span class="fw-bold fs-5 text-primary">Rs {{ cartStore.subtotal.toLocaleString('en-IN') }}</span>
              </div>

              <div class="mb-3">
                <label for="deliveryAddress" class="form-label small">Delivery Address</label>
                <textarea id="deliveryAddress" class="form-control" rows="3" v-model="deliveryAddress"
                  placeholder="Enter your complete delivery address" required maxlength="250"
                  style="resize: none;"></textarea>
                <div class="form-text">
                  Maximum 250 characters allowed.
                </div>
              </div>

              <button class="btn btn-primary w-100 py-3 checkout-btn" @click="checkout"
                :disabled="cartStore.items.length === 0 || !deliveryAddress">
                <i class="bi bi-lock-fill me-2"></i> Proceed to Checkout
              </button>

              <div class="mt-4 text-center">
                <div class="payment-methods d-flex justify-content-center gap-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="25" viewBox="0 0 576 512">
                    <path fill="#5D5D5D"
                      d="M470.1 231.3s7.6 37.2 9.3 45H446c3.3-8.9 16-43.5 16-43.5-.2.3 3.3-9.1 5.3-14.9l2.8 13.4zM576 80v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48h480c26.5 0 48 21.5 48 48zM152.5 331.2L215.7 176h-42.5l-39.3 106-4.3-21.5-14-56.1c-2.1-8.5-9.7-12.7-16.3-8.2l-13 9.2 38.8 144.8h42.5zm94.4.2L272.1 176h-40.2l-25.1 155.4h40.1zm139.9-50.8c.2-17.7-10.6-31.2-33.7-42.3-14.1-7.1-22.7-11.9-22.7-19.2.2-6.6 7.3-13.4 23.1-13.4 13.1-.3 22.7 2.8 29.9 5.9l3.6 1.7 5.5-33.6c-7.9-3.1-20.5-6.6-36-6.6-39.7 0-67.6 21.2-67.8 51.4-.3 22.3 20 34.7 35.2 42.2 15.5 7.6 20.8 12.6 20.8 19.3-.2 10.4-12.6 15.2-24.1 15.2-16 0-24.6-2.5-37.7-8.3l-5.3-2.5-5.6 34.9c9.4 4.3 26.8 8.1 44.8 8.3 42.2.1 69.7-20.8 70-53zM528 176h-44.4c-6.3 0-11.4 5.1-11.4 11.4v136.2c0 6.9 5.6 12.5 12.5 12.5h35.9c6.9 0 12.5-5.6 12.5-12.5V187.4c.1-6.3-5-11.4-11.1-11.4z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="25" viewBox="0 0 512 512">
                    <path fill="#5D5D5D"
                      d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="25" viewBox="0 0 512 512">
                    <path fill="#5D5D5D"
                      d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z" />
                  </svg>
                </div>
                <p class="small text-muted mt-2">Secure payment processing</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <div v-if="showPaymentModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);"
      tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Payment Method</h5>
            <button type="button" class="btn-close" @click="closePaymentModal"></button>
          </div>
          <div class="modal-body">
            <div class="d-grid gap-3">
              <button class="btn btn-outline-primary py-3" @click="selectPaymentMethod('cash')">
                <i class="bi bi-truck me-2"></i> Cash on Delivery
              </button>
              <button class="btn btn-outline-success py-3" @click="selectPaymentMethod('online')">
                <i class="bi bi-credit-card me-2"></i> Online Payment
              </button>
              <button class="btn btn-outline-dark py-3" @click="showGooglePay">
                <img src="https://img.icons8.com/color/48/000000/google-pay.png"
                  style="height: 24px; margin-right: 10px;" />
                Google Pay
              </button>

            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showStripeModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);"
      tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Secure Payment</h5>
            <button type="button" class="btn-close" @click="closeStripeModal"></button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row justify-content-center">
                <div class="col-md-8">
                  <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                      <h5 class="mb-0">Complete Your Payment</h5>
                    </div>
                    <div class="card-body">
                      <div v-if="paymentSuccess" class="text-center py-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Payment Successful!</h3>
                        <p class="text-muted">Your payment of Rs {{ cartStore.subtotal.toLocaleString('en-IN') }} has
                          been processed.</p>
                        <button class="btn btn-primary" @click="proceedWithOrder('online')">
                          Continue with Order
                        </button>
                      </div>
                      <div v-else-if="paymentError" class="text-center py-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Payment Failed</h3>
                        <p class="text-danger">{{ paymentErrorMessage }}</p>
                        <div class="d-flex justify-content-center gap-3">
                          <button class="btn btn-primary" @click="retryPayment">
                            <i class="bi bi-arrow-repeat me-2"></i> Try Again
                          </button>
                          <button class="btn btn-outline-secondary" @click="closeStripeModal">
                            Change Payment Method
                          </button>
                        </div>
                      </div>
                      <div v-else>
                        <div class="alert alert-info">
                          <i class="bi bi-info-circle me-2"></i> You will be charged Rs {{
                            cartStore.subtotal.toLocaleString('en-IN') }}
                        </div>
                        <form id="stripe-payment-form">
                          <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" v-model="customerEmail" required>
                          </div>

                          <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" v-model="cardNumber"
                              placeholder="4242 4242 4242 4242" required maxlength="19"
                              @input="cardNumber = cardNumber.replace(/[^0-9 ]/g, '')">
                          </div>

                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label class="form-label">Expiry Date</label>
                              <input type="text" class="form-control" v-model="cardExpiry" placeholder="MM/YY" required
                                maxlength="5" @input="cardExpiry = cardExpiry.replace(/[^0-9/]/g, '')">
                            </div>

                            <div class="col-md-6 mb-3">
                              <label class="form-label">CVC</label>
                              <input type="text" class="form-control" v-model="cardCvc" placeholder="123" required
                                maxlength="4" @input="cardCvc = cardCvc.replace(/[^0-9]/g, '')">
                            </div>
                          </div>

                          <button type="button" id="submit-payment" class="btn btn-primary w-100 py-2"
                            @click="processStripePayment" :disabled="processingPayment">
                            <span v-if="processingPayment">
                              <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                              Processing...
                            </span>
                            <span v-else>
                              Pay Rs {{ cartStore.subtotal.toLocaleString('en-IN') }}
                            </span>
                          </button>
                        </form>

                      </div>
                    </div>
                    <div class="card-footer text-muted small">
                      <div class="d-flex justify-content-center gap-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" width="40"
                          alt="Visa Logo">

                        <img
                          src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg"
                          width="40">
                        <img src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5cd6a96a6e418a6ca1717c.svg"
                          width="40">
                      </div>
                      <p class="text-center mt-2 mb-0">Your payment is secure and encrypted</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useCartStore } from '../stores/cartStore';
import { useCustomerStore } from '../stores/customerStore';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const cartStore = useCartStore();
const customerStore = useCustomerStore();
const router = useRouter();

const deliveryAddress = ref('');
const isLoading = ref(false);
const showPaymentModal = ref(false);
const showStripeModal = ref(false);
const paymentSuccess = ref(false);
const paymentError = ref(false);
const paymentErrorMessage = ref('');
const processingPayment = ref(false);
const customerEmail = ref(customerStore.email || '');
const cardNumber = ref('');
const cardExpiry = ref('');
const cardCvc = ref('');

const increaseQuantity = (item) => {
  if (item.quantity < item.stock) {
    cartStore.updateQuantity(item.id, item.quantity + 1);
  }
};

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    cartStore.updateQuantity(item.id, item.quantity - 1);
  }
};

const removeItem = (id) => {
  cartStore.removeFromCart(id);
};

const checkout = async () => {
  if (!deliveryAddress.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Address Required',
      text: 'Please enter your delivery address to proceed',
      confirmButtonColor: '#3085d6',
    });
    return;
  }

  showPaymentModal.value = true;
};

const closePaymentModal = () => {
  showPaymentModal.value = false;
};

const closeStripeModal = () => {
  showStripeModal.value = false;
  paymentSuccess.value = false;
  paymentError.value = false;
  processingPayment.value = false;
  cardNumber.value = '';
  cardExpiry.value = '';
  cardCvc.value = '';
};

const selectPaymentMethod = (method) => {
  showPaymentModal.value = false;

  if (method === 'online') {
    showStripeModal.value = true;
  } else {
    placeOrder('cash');
  }
};

const processStripePayment = async () => {
  if (!validateCardDetails()) {
    return;
  }

  processingPayment.value = true;

  try {
    const response = await axios.post('/stripe', {
      amount: cartStore.subtotal,
      stripeToken: 'tok_visa',
      email: customerEmail.value
    });

    if (response.data.success) {
      paymentSuccess.value = true;
    } else {
      throw new Error(response.data.message || 'Payment failed');
    }
  } catch (error) {
    paymentError.value = true;
    //  error.response?.data?.message || error.message ||
    paymentErrorMessage.value = 'Payment processing failed. Please try again.';
    console.error('Payment error:', error);
  } finally {
    processingPayment.value = false;
  }
};

const validateCardDetails = () => {
  if (!customerEmail.value) {
    Swal.fire({
      icon: 'error',
      title: 'Email Required',
      text: 'Please enter your email address',
      confirmButtonColor: '#3085d6',
    });
    return false;
  }

  if (!cardNumber.value || cardNumber.value.replace(/\s/g, '').length !== 16) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Card',
      text: 'Please enter a valid 16-digit card number',
      confirmButtonColor: '#3085d6',
    });
    return false;
  }

  if (!cardExpiry.value || !cardExpiry.value.includes('/')) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Expiry',
      text: 'Please enter expiry date in MM/YY format',
      confirmButtonColor: '#3085d6',
    });
    return false;
  }

  if (!cardCvc.value || cardCvc.value.length < 3) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid CVC',
      text: 'Please enter a valid 3-digit CVC code',
      confirmButtonColor: '#3085d6',
    });
    return false;
  }

  return true;
};

const retryPayment = () => {
  paymentError.value = false;
  paymentErrorMessage.value = '';
  cardNumber.value = '';
  cardExpiry.value = '';
  cardCvc.value = '';
};

const proceedWithOrder = (method) => {
  closeStripeModal();
  placeOrder(method);
};

const placeOrder = async (paymentMethod) => {
  isLoading.value = true;

  try {
    const orderData = {
      customer_id: customerStore.id,
      delivery_address: deliveryAddress.value,
      products: cartStore.items.map(item => ({
        product_id: item.id,
        quantity: item.quantity
      })),
      order_type: paymentMethod
    };

    const response = await axios.post('/place-order', orderData);

    if (response.data.success) {
      await Swal.fire({
        title: 'Order Placed Successfully!',
        html: `
          <div class="text-center">
            <div class="swal2-icon-success">
              <div class="swal2-success-circular-line-left"></div>
              <span class="swal2-success-line-tip"></span>
              <span class="swal2-success-line-long"></span>
              <div class="swal2-success-ring"></div>
              <div class="swal2-success-fix"></div>
              <div class="swal2-success-circular-line-right"></div>
            </div>
            <p>Your order ID: <strong>${response.data.order_id}</strong></p>
            <p>Total Paid: <strong>Rs ${response.data.total_payment.toLocaleString('en-IN')}</strong></p>
          </div>
        `,
        confirmButtonText: 'View Order',
        showCancelButton: true,
        cancelButtonText: 'Continue Shopping',
        confirmButtonColor: '#3085d6',
      }).then((result) => {
        if (result.isConfirmed) {
          router.push(`/orders`);
        } else {
          router.push('/home');
        }
      });

      cartStore.clearCart();
    } else {
      throw new Error(response.data.message || 'Order failed');
    }
  } catch (error) {
    let errorMessage = 'Failed to place order. Please try again.';

    if (error.response?.data?.errors) {
      const stockIssues = error.response.data.errors;
      errorMessage = stockIssues.map(issue =>
        `<div class="text-left mb-2">
          <strong>${issue.product_name}</strong><br>
          Requested: ${issue.requested_quantity}, Available: ${issue.available_stock}
        </div>`
      ).join('');

      await Swal.fire({
        title: 'Stock Issues',
        html: `
          <div class="text-danger">
            <p>Some items in your cart have insufficient stock:</p>
            ${errorMessage}
          </div>
        `,
        icon: 'error',
        confirmButtonText: 'Update Cart',
        confirmButtonColor: '#3085d6',
      });

      const updatedItems = cartStore.items.filter(item => {
        const stockError = stockIssues.find(issue => issue.product_id === item.id);
        if (stockError) {
          if (stockError.available_stock > 0) {
            cartStore.updateQuantity(item.id, stockError.available_stock);
          } else {
            return false;
          }
        }
        return true;
      });

      cartStore.setItems(updatedItems);
    } else {
      await Swal.fire({
        title: 'Error',
        text: errorMessage,
        icon: 'error',
        confirmButtonColor: '#3085d6',
      });
    }
  } finally {
    isLoading.value = false;
  }
};
</script>



<style scoped>
.cart-view {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.cart-hero {
  background-image: url('/images/back_1.jpg');
  background-size: cover;
  background-position: center;
  padding: 4rem 0;
  margin-bottom: 2rem;
  position: relative;
}

.cart-hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
}

.cart-hero .container {
  position: relative;
  z-index: 1;
}

.cart-hero h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: white;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.cart-hero .lead {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
}

.cart-hero-header {
  position: relative;
  z-index: 1;
}

.cart-hero-header h1,
.cart-hero-header .lead {
  color: white;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.cart-item {
  transition: all 0.3s ease;
  border: 1px solid #eee;
}

.cart-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.quantity-control {
  max-width: 140px;
}

.quantity-control .btn {
  padding: 0.25rem 0.75rem;
}

.quantity-control input {
  text-align: center;
  padding: 0.25rem;
  width: 40px;
}

.empty-cart {
  background: white;
  border-radius: 10px;
  padding: 3rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.order-summary {
  border: 1px solid #eee;
  position: sticky;
  top: 20px;
}

.checkout-btn {
  transition: all 0.3s ease;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.checkout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.checkout-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.payment-methods svg {
  opacity: 0.7;
  transition: all 0.3s ease;
}

.payment-methods svg:hover {
  opacity: 1;
  transform: scale(1.1);
}

.modal {
  backdrop-filter: blur(5px);
}

.modal-content {
  border: none;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
}

.modal-header {
  border-bottom: 1px solid #eee;
}


.form-control {
  padding: 10px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
}

.form-control:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
}


@media (max-width: 992px) {
  .order-summary {
    position: static;
    margin-top: 2rem;
  }
}

@media (max-width: 768px) {
  .cart-hero {
    padding: 3rem 0;
    text-align: center;
  }

  .cart-hero h1 {
    font-size: 2rem;
  }

  .cart-hero .btn {
    margin-top: 1rem;
  }

  .cart-item {
    padding: 1.5rem;
  }

  .quantity-control {
    max-width: 120px;
  }
}

@media (max-width: 576px) {
  .cart-hero {
    padding: 2.5rem 0;
  }

  .cart-hero h1 {
    font-size: 1.75rem;
  }

  .cart-hero .lead {
    font-size: 1rem;
  }

  .empty-cart {
    padding: 2rem 1rem;
  }

  .empty-cart h3 {
    font-size: 1.5rem;
  }

  .modal-dialog {
    margin: 0.5rem;
  }

  .modal-body {
    padding: 1rem;
  }
}
/* Default (desktop) */
.quantity-btn {
  padding: 0.4rem 0.75rem;
  font-size: 1rem;
}

.quantity-input {
  font-size: 1rem;
  min-width: 50px;
}

/* Responsive for less than 800px */
@media (max-width: 800px) {
  .quantity-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
  }

  .quantity-input {
    font-size: 0.85rem;
    min-width: 40px;
  }
}

/* Extra small (like 270px) */
@media (max-width: 300px) {
  .quantity-btn {
    padding: 0.2rem 0.3rem;
    font-size: 0.75rem;
  }

  .quantity-input {
    font-size: 0.75rem;
    min-width: 30px;
  }
}

</style>