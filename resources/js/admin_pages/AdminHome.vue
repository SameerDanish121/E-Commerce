<template>
  <div class="admin-products-page">
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Product Management</h1>
          <button v-if="adminStore.hasPermission('Add Product')" class="btn btn-primary" @click="openAddProductModal">
            <i class="bi bi-plus-circle me-2"></i>Add Product
          </button>
        </div>
      </div>
    </section>

    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Products</label>
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Search by name or description..."
              @input="handleSearch">
          </div>
          <div class="col-md-3">
            <label class="form-label">Category</label>
            <select v-model="selectedCategory" class="form-select" @change="handleCategoryChange">
              <option value="all">All Categories</option>
              <option v-for="category in uniqueCategories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select v-model="selectedStatus" class="form-select" @change="handleStatusChange">
              <option value="all">All Statuses</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              Reset Filters
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="products-table-section py-4">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sold</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="adminProductStore.isLoading">
                    <td colspan="8" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredProducts.length === 0">
                    <td colspan="8" class="text-center py-5">
                      <h5>No products found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="product in filteredProducts" :key="product.id"
                    :class="{ 'table-secondary': !product.is_active }">
                    <td>
                      <img :src="product.image_url || '/images/placeholder-product.png'" :alt="product.name"
                        class="product-thumbnail">
                    </td>
                    <td>
                      <strong>{{ product.name }}</strong>
                      <!-- <p class="text-muted small mb-0 truncate-text">{{ product.description }}</p> -->
                      <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                        {{ product.description }}
                      </p>

                    </td>
                    <td>{{ product.category }}</td>
                    <td>Rs {{ product.price.toFixed(2) }}</td>
                    <td>
                      <span :class="{ 'text-danger': product.stock < 10 }">
                        {{ product.stock }}
                      </span>
                    </td>
                    <td>{{ product.units_sold || 0 }}</td>
                    <td>
                      <span class="badge" :class="product.is_active ? 'bg-success' : 'bg-secondary'">
                        {{ product.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="openViewModal(product)"
                          title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button v-if="adminStore.hasPermission('Edit Product')" class="btn btn-sm btn-outline-secondary"
                          @click="openEditModal(product)" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button v-if="adminStore.hasPermission('Remove Product')" class="btn btn-sm"
                          :class="product.is_active ? 'btn-outline-danger' : 'btn-outline-success'"
                          @click="toggleProductStatus(product)" :title="product.is_active ? 'Deactivate' : 'Activate'">
                          <i :class="product.is_active ? 'bi bi-x-circle' : 'bi bi-check-circle'"></i>
                        </button>
                        <button v-if="adminStore.hasPermission('Re Stock Product')"
                          class="btn btn-sm btn-outline-warning" @click="openRestockModal(product)" title="Restock">
                          <i class="bi bi-box-seam"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Product Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="viewedProduct" class="row">
              <div class="col-md-5">
                <img :src="viewedProduct.image_url || '/images/placeholder-product.png'" :alt="viewedProduct.name"
                  class="img-fluid rounded mb-3">
              </div>
              <div class="col-md-7">
                <h3>{{ viewedProduct.name }}</h3>
                <p class="text-muted">{{ viewedProduct.category }}</p>

                <div class="d-flex align-items-center mb-3">
                  <h4 class="mb-0 me-3">Rs {{ viewedProduct.price.toFixed(2) }}</h4>
                  <span class="badge" :class="viewedProduct.is_active ? 'bg-success' : 'bg-secondary'">
                    {{ viewedProduct.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>

                <div class="row mb-3">
                  <div class="col-6">
                    <div class="card bg-light">
                      <div class="card-body py-2">
                        <small class="text-muted">Current Stock</small>
                        <h5 class="mb-0">{{ viewedProduct.stock }}</h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="card bg-light">
                      <div class="card-body py-2">
                        <small class="text-muted">Units Sold</small>
                        <h5 class="mb-0">{{ viewedProduct.units_sold || 0 }}</h5>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- <h5>Description</h5>
                <p class="text-muted">{{ viewedProduct.description || 'No description available' }}</p> -->
                <div class="card bg-light p-3">
                  <h5>Description</h5>
                  <p class="text-muted text-wrap text-break m-0">
                    {{ viewedProduct.description || 'No description available' }}
                  </p>
                </div>

              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="productFormModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <!-- <div class="modal-body">
            <form @submit.prevent="handleProductSubmit">
              <div class="row">
             
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input v-model="productForm.name" type="text" class="form-control" maxlength="100"
                      pattern=".{1,100}" required placeholder="Enter product name">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input v-model="productForm.category" type="text" class="form-control" maxlength="50"
                      pattern=".{1,50}" required placeholder="Enter product category">
                  </div>
                  <label class="form-label">Price</label>
                  <div class="input-group">
                    <span class="input-group-text">Rs</span>
                    <input v-model="productForm.price" type="number" class="form-control" min="1" max="1000000"
                      step="0.01" required placeholder="e.g., 499.99"
                      oninput="if (this.value.length > 0 && (parseFloat(this.value) > 1000000)) this.value = 1000000;" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input v-model="productForm.stock" type="number" class="form-control" min="0" max="1000000" required
                      placeholder="e.g., 100"
                      oninput="if (this.value.length > 0 && parseInt(this.value) > 1000000) this.value = 1000000;" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select v-model="productForm.is_active" class="form-select">
                      <option :value="true">Active</option>
                      <option :value="false">Inactive</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" class="form-control" accept="image/*" @change="handleImageUpload"
                      ref="fileInput">
                    <small class="text-muted">Only jpeg, png, jpg (max 2MB)</small>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="productForm.description" class="form-control" rows="3" maxlength="255"
                  style="resize: none;"></textarea>
                <div class="form-text">
                  {{ productForm.description.length }}/255 characters
                </div>
              </div>


              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? 'Update Product' : 'Add Product' }}
                </button>
              </div>
            </form>
          </div> -->

         <div class="modal-body">
  <form @submit.prevent="validateAndSubmit">
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input v-model="productForm.name" 
                 @blur="validateField('name')"
                 :class="{'is-invalid': errors.name}"
                 type="text" class="form-control" 
                 maxlength="100" required 
                 placeholder="Enter product name">
          <div class="invalid-feedback">{{ errors.name }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <input v-model="productForm.category" 
                 @blur="validateField('category')"
                 :class="{'is-invalid': errors.category}"
                 type="text" class="form-control" 
                 maxlength="50" required 
                 placeholder="Enter product category">
          <div class="invalid-feedback">{{ errors.category }}</div>
        </div>
        <label class="form-label">Price</label>
        <div class="input-group mb-3">
          <span class="input-group-text">Rs</span>
          <input v-model="productForm.price" 
                 @blur="validateField('price')"
                 :class="{'is-invalid': errors.price}"
                 type="number" class="form-control" 
                 min="1" max="1000000" step="0.01" required 
                 placeholder="e.g., 499.99">
        </div>
        <div class="invalid-feedback d-block">{{ errors.price }}</div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Stock Quantity</label>
          <input v-model="productForm.stock" 
                 @blur="validateField('stock')"
                 :class="{'is-invalid': errors.stock}"
                 type="number" class="form-control" 
                 min="0" max="1000000" required 
                 placeholder="e.g., 100">
          <div class="invalid-feedback">{{ errors.stock }}</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select v-model="productForm.is_active" class="form-select">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Product Image</label>
          <input type="file" 
                 class="form-control" 
                 :class="{'is-invalid': errors.image}"
                 accept="image/*" 
                 @change="handleImageUpload"
                 ref="fileInput">
          <small class="text-muted">Only jpeg, png, jpg (max 2MB)</small>
          <div class="invalid-feedback">{{ errors.image }}</div>
        </div>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea v-model="productForm.description" 
                @blur="validateField('description')"
                :class="{'is-invalid': errors.description}"
                class="form-control" rows="3" maxlength="255"
                style="resize: none;"></textarea>
      <div class="form-text">
        {{ productForm.description.length }}/255 characters
      </div>
      <div class="invalid-feedback">{{ errors.description }}</div>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cancel
      </button>
      <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
        {{ isEditing ? 'Update Product' : 'Add Product' }}
      </button>
    </div>
  </form>
</div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Restock Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleRestockSubmit">
              <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" :value="restockProductData.name" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">Current Stock</label>
                <input type="number" class="form-control" :value="restockProductData.stock" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">Quantity to Add</label>
                <input v-model="restockQuantity" type="number" class="form-control" min="1" max="1000000" required
                  oninput="if (this.value > 1000000) this.value = 1000000" />

              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  Confirm Restock
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAdminProductStore } from '../stores/adminProductStore';
import { Modal } from 'bootstrap';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import { useAdminStore } from '../stores/adminStore';
const toast = useToast();
const adminStore = useAdminStore();
const adminProductStore = useAdminProductStore();

let viewProductModal = null;
let productFormModal = null;
let restockModal = null;

const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedStatus = ref('all');

const isEditing = ref(false);
const isSubmitting = ref(false);
const productForm = ref({
  id: null,
  name: '',
  category: '',
  description: '',
  price: 0,
  stock: 0,
  is_active: true,
  image: null
});
const fileInput = ref(null);

const viewedProduct = ref(null);

const restockProductData = ref({});
const restockQuantity = ref(1);



onMounted(async () => {
  await adminProductStore.fetchProducts();
  viewProductModal = new Modal(document.getElementById('viewProductModal'));
  productFormModal = new Modal(document.getElementById('productFormModal'));
  restockModal = new Modal(document.getElementById('restockModal'));

});
const uniqueCategories = computed(() => {
  const categories = new Set();
  adminProductStore.adminProducts.forEach(product => {
    if (product.category) {
      categories.add(product.category);
    }
  });
  return Array.from(categories).sort();
});

const filteredProducts = computed(() => {
  return adminProductStore.adminProducts.filter(product => {
    const matchesSearch = searchQuery.value === '' ||
      product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (product.description && product.description.toLowerCase().includes(searchQuery.value.toLowerCase()));


    const matchesCategory = selectedCategory.value === 'all' ||
      product.category === selectedCategory.value;

    const matchesStatus = selectedStatus.value === 'all' ||
      (selectedStatus.value === 'active' && product.is_active) ||
      (selectedStatus.value === 'inactive' && !product.is_active);

    return matchesSearch && matchesCategory && matchesStatus;
  });
});


const handleSearch = () => {

};

const handleCategoryChange = () => {

};

const handleStatusChange = () => {

};

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = 'all';
  selectedStatus.value = 'all';
};

const openViewModal = (product) => {
  viewedProduct.value = { ...product };
  viewProductModal.show();
};

const openAddProductModal = () => {
  isEditing.value = false;
  resetProductForm();
  productFormModal.show();
};

const openEditModal = (product) => {
  isEditing.value = true;
  productForm.value = {
    id: product.id,
    name: product.name,
    category: product.category,
    description: product.description,
    price: product.price,
    stock: product.stock,
    is_active: Boolean(product.is_active),
    image: null
  };
  productFormModal.show();
};

const resetProductForm = () => {
  productForm.value = {
    id: null,
    name: '',
    category: '',
    description: '',
    price: 0,
    stock: 0,
    is_active: true,
    image: null
  };
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// const handleImageUpload = (event) => {
//   const file = event.target.files[0];
//   if (file) {
//     productForm.value.image = file;
//   }
// };
// Add these to your component's script
const errors = ref({
  name: '',
  category: '',
  price: '',
  stock: '',
  description: '',
  image: ''
});

const validateField = (field) => {
  switch (field) {
    case 'name':
      if (!productForm.value.name) {
        errors.value.name = 'Product name is required';
      } else if (productForm.value.name.length > 100) {
        errors.value.name = 'Name must be less than 100 characters';
      } else {
        errors.value.name = '';
      }
      break;

    case 'category':
      if (!productForm.value.category) {
        errors.value.category = 'Category is required';
      } else if (productForm.value.category.length > 50) {
        errors.value.category = 'Category must be less than 50 characters';
      } else {
        errors.value.category = '';
      }
      break;

    case 'price':
      if (!productForm.value.price) {
        errors.value.price = 'Price is required';
      } else if (parseFloat(productForm.value.price) <= 0) {
        errors.value.price = 'Price must be greater than 0';
      } else if (parseFloat(productForm.value.price) > 1000000) {
        errors.value.price = 'Price must be less than 1,000,000';
      } else {
        errors.value.price = '';
      }
      break;

    case 'stock':
      if (productForm.value.stock === '' || productForm.value.stock === null) {
        errors.value.stock = 'Stock quantity is required';
      } else if (parseInt(productForm.value.stock) < 0) {
        errors.value.stock = 'Stock cannot be negative';
      } else if (parseInt(productForm.value.stock) > 1000000) {
        errors.value.stock = 'Stock must be less than 1,000,000';
      } else {
        errors.value.stock = '';
      }
      break;

    case 'description':
      if (productForm.value.description.length > 255) {
        errors.value.description = 'Description must be less than 255 characters';
      } else {
        errors.value.description = '';
      }
      break;

    case 'image':
      // Optional field, but if file is selected, validate it
      if (productForm.value.image) {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(productForm.value.image.type)) {
          errors.value.image = 'Only JPEG, PNG, or JPG images are allowed';
        } else if (productForm.value.image.size > maxSize) {
          errors.value.image = 'Image size must be less than 2MB';
        } else {
          errors.value.image = '';
        }
      }
      break;
  }
};

const validateForm = () => {
  validateField('name');
  validateField('category');
  validateField('price');
  validateField('stock');
  validateField('description');
  validateField('image');

  return !Object.values(errors.value).some(error => error !== '');
};



const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    productForm.value.image = file;
    validateField('image');
  } else {
    productForm.value.image = null;
    errors.value.image = '';
  }
};



const validateAllFields = () => {
  let isValid = true;
  
  // Validate name
  if (!productForm.value.name) {
    errors.value.name = 'Product name is required';
    isValid = false;
  } else if (productForm.value.name.length > 100) {
    errors.value.name = 'Name must be less than 100 characters';
    isValid = false;
  } else {
    errors.value.name = '';
  }
  
  // Validate category
  if (!productForm.value.category) {
    errors.value.category = 'Category is required';
    isValid = false;
  } else if (productForm.value.category.length > 50) {
    errors.value.category = 'Category must be less than 50 characters';
    isValid = false;
  } else {
    errors.value.category = '';
  }
  
  // Validate price
  if (!productForm.value.price) {
    errors.value.price = 'Price is required';
    isValid = false;
  } else if (parseFloat(productForm.value.price) <= 0) {
    errors.value.price = 'Price must be greater than 0';
    isValid = false;
  } else if (parseFloat(productForm.value.price) > 1000000) {
    errors.value.price = 'Price must be less than 1,000,000';
    isValid = false;
  } else {
    errors.value.price = '';
  }
  
  // Validate stock
  if (productForm.value.stock === '' || productForm.value.stock === null) {
    errors.value.stock = 'Stock quantity is required';
    isValid = false;
  } else if (parseInt(productForm.value.stock) < 0) {
    errors.value.stock = 'Stock cannot be negative';
    isValid = false;
  } else if (parseInt(productForm.value.stock) > 1000000) {
    errors.value.stock = 'Stock must be less than 1,000,000';
    isValid = false;
  } else {
    errors.value.stock = '';
  }
  
  // Validate description
  if (productForm.value.description.length > 255) {
    errors.value.description = 'Description must be less than 255 characters';
    isValid = false;
  } else {
    errors.value.description = '';
  }
  
  // Validate image (optional)
  if (productForm.value.image) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!validTypes.includes(productForm.value.image.type)) {
      errors.value.image = 'Only JPEG, PNG, or JPG images are allowed';
      isValid = false;
    } else if (productForm.value.image.size > maxSize) {
      errors.value.image = 'Image size must be less than 2MB';
      isValid = false;
    } else {
      errors.value.image = '';
    }
  }
  
  return isValid;
};

const validateAndSubmit = async () => {
  const isValid = validateAllFields();
  
  if (!isValid) {
    toast.error('Please fix all validation errors before submitting');
    return;
  }
  
  await handleProductSubmit();
};

// Keep your existing handleProductSubmit function unchanged
const handleProductSubmit = async () => {
  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append('name', productForm.value.name);
    formData.append('category', productForm.value.category);
    formData.append('description', productForm.value.description);
    formData.append('price', productForm.value.price);
    formData.append('stock', productForm.value.stock);
    formData.append('is_active', productForm.value.is_active ? 1 : 0);
    if (productForm.value.image) {
      formData.append('product_pic', productForm.value.image);
    }
    if (isEditing.value) {
      await adminProductStore.updateProduct(productForm.value.id, formData);
      toast.success('Product updated successfully!');
      await adminProductStore.fetchProducts();
    } else {
      await adminProductStore.addProduct(formData);
      toast.success('Product added successfully!');
      await adminProductStore.fetchProducts();
    }
    await adminProductStore.fetchProducts();
    productFormModal.hide();
    resetProductForm();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors;
      for (const field in errors) {
        toast.error(`${field}: ${errors[field][0]}`);
      }
    } else {
      toast.error(error.message || 'Failed to save product');
    }
  } finally {
    isSubmitting.value = false;
  }
};
// const handleProductSubmit = async () => {
//   isSubmitting.value = true;
//   try {
//     const formData = new FormData();
//     formData.append('name', productForm.value.name);
//     formData.append('category', productForm.value.category);
//     formData.append('description', productForm.value.description);
//     formData.append('price', productForm.value.price);
//     formData.append('stock', productForm.value.stock);
//     formData.append('is_active', productForm.value.is_active ? 1 : 0);
//     if (productForm.value.image) {
//       formData.append('product_pic', productForm.value.image);
//     }
//     if (isEditing.value) {
//       await adminProductStore.updateProduct(productForm.value.id, formData);
//       toast.success('Product updated successfully!');
//        await adminProductStore.fetchProducts();
//     } else {
//       await adminProductStore.addProduct(formData);
//       toast.success('Product added successfully!');
//         await adminProductStore.fetchProducts();
//     }
//     await adminProductStore.fetchProducts();

//     productFormModal.hide();
//     resetProductForm();
//   } catch (error) {
//     if (error.response && error.response.status === 422) {
//       const errors = error.response.data.errors;
//       for (const field in errors) {
//         toast.error(`${field}: ${errors[field][0]}`);
//       }
//     } else {
//       toast.error(error.message || 'Failed to save product');
//     }
//   } finally {
//     isSubmitting.value = false;
//   }
// };

const toggleProductStatus = async (product) => {
  try {
    await adminProductStore.toggleProductStatus(product.id);
    toast.success(`Product ${product.is_active ? 'deactivated' : 'activated'} successfully!`);

    await adminProductStore.fetchProducts();
  } catch (error) {
    toast.error(error.message || 'Failed to toggle product status');
  }
};

const openRestockModal = (product) => {
  restockProductData.value = { ...product };
  restockQuantity.value = 1;
  restockModal.show();
};

const handleRestockSubmit = async () => {
  isSubmitting.value = true;

  try {
    await adminProductStore.restockProduct(
      restockProductData.value.id,
      restockQuantity.value
    );
    toast.success('Product restocked successfully!');
    await adminProductStore.fetchProducts();
    restockModal.hide();
  } catch (error) {
    toast.error(error.message || 'Failed to restock product');
  } finally {
    isSubmitting.value = false;
  }
};

</script>

<style scoped>
.admin-products-page {
  padding-top: 1rem;
}

.admin-header {
  border-bottom: 1px solid #eee;
  margin-bottom: 1.5rem;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.product-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.truncate-text {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

@media (max-width: 992px) {
  .page-title {
    font-size: 1.5rem;
  }
}

@media (max-width: 768px) {
  .filters-section .row>div {
    margin-bottom: 0.5rem;
  }

  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table {
    width: 100%;
    margin-bottom: 1rem;
    display: block;
  }

  .table thead {
    display: none;
  }

  .table tbody {
    display: block;
    width: 100%;
  }

  .table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    position: relative;
    padding-left: 50%;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    width: 45%;
    padding-right: 1rem;
    font-weight: 600;
    text-align: left;
  }

  .table td:last-child {
    border-bottom: 0;
  }

  .table td:nth-child(1)::before {
    content: "Image";
  }

  .table td:nth-child(2)::before {
    content: "Name";
  }

  .table td:nth-child(3)::before {
    content: "Category";
  }

  .table td:nth-child(4)::before {
    content: "Price";
  }

  .table td:nth-child(5)::before {
    content: "Stock";
  }

  .table td:nth-child(6)::before {
    content: "Sold";
  }

  .table td:nth-child(7)::before {
    content: "Status";
  }

  .table td:nth-child(8)::before {
    content: "Actions";
  }

  .product-thumbnail {
    width: 40px;
    height: 40px;
  }

  .table td .d-flex {
    justify-content: flex-end;
  }
}

@media (max-width: 576px) {
  .admin-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .page-title {
    margin-bottom: 1rem;
  }

  .modal-dialog {
    margin: 0.5rem auto;
  }
}
</style>