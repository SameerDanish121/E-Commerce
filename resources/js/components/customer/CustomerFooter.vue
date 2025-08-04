<template>
  <footer class="py-5 bg-light">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="footer-menu">
            <h5 class="mb-4">ShopMaster</h5>
            <p>Your one-stop shop for all your grocery needs.</p>
            <div class="social-links mt-4">
              <a href="https://www.facebook.com/openai" target="_blank" class="btn btn-outline-dark me-2 mb-2">
                <i class="bi bi-facebook"></i>
              </a>
              <a href="https://twitter.com/opensourceorg" target="_blank" class="btn btn-outline-dark me-2 mb-2">
                <i class="bi bi-twitter"></i>
              </a>
              <a href="https://www.instagram.com/natgeo/" target="_blank" class="btn btn-outline-dark me-2 mb-2">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-2 col-6" v-for="(links, index) in footerLinks" :key="index">
          <div class="footer-menu">
            <h5 class="mb-4">{{ links.title }}</h5>
            <ul class="list-unstyled">
              <li v-for="(link, i) in links.items" :key="i" class="mb-2">
                <!-- <router-link :to="link.path" class="text-decoration-none text-muted"> -->
                <!-- <h2     class="text-decoration-none text-muted">{{ link.text }}</h2> -->

                <!-- </router-link> -->
                <div class="text-decoration-none text-muted">
                  {{ link.text }}
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <div class="footer-bottom py-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <p class="mb-0">© {{ new Date().getFullYear() }} ShopMaster. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="mb-0">Made with ❤️ for our customers</p>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
export default {
  name: 'CustomerFooter',
  data() {
    return {
      // footerLinks: [
      //   {
      //     title: 'Shop',
      //     items: [
      //       { text: 'All Products', path: '/home' },
      //       { text: 'Featured', path: '/home?filter=featured' },
      //       { text: 'New Arrivals', path: '/home?filter=new' },
      //       { text: 'Deals', path: '/home?filter=deals' }
      //     ]
      //   },
      //   {
      //     title: 'Help',
      //     items: [
      //       { text: 'Contact Us', path: '/help/contact' },
      //       { text: 'FAQs', path: '/help/faqs' },
      //       { text: 'Shipping', path: '/help/shipping' },
      //       { text: 'Returns', path: '/help/returns' }
      //     ]
      //   },
      //   {
      //     title: 'Company',
      //     items: [
      //       { text: 'About Us', path: '/about' },
      //       { text: 'Blog', path: '/blog' },
      //       { text: 'Careers', path: '/careers' },
      //       { text: 'Privacy Policy', path: '/privacy' }
      //     ]
      //   }
      // ]
      footerLinks: [
        {
          title: 'Shop',
          items: [
            { text: 'Browse our full catalog of products.' },
            { text: 'Exclusive picks updated weekly.' },
            { text: 'Fresh arrivals just in stock.' },
            { text: 'Save big on seasonal deals.' }
          ]
        },
        {
          title: 'Help',
          items: [
            { text: 'Need assistance? We’re here 24/7.' },
            { text: 'Most common customer questions answered.' },
            { text: 'Details about our delivery process.' },
            { text: 'Easy and flexible return policy.' }
          ]
        },
        {
          title: 'Company',
          items: [
            { text: 'We’re passionate about quality and service.' },
            { text: 'Insights and stories from our team.' },
            { text: 'Building a better experience for everyone.' },
            { text: 'Your data privacy is important to us.' }
          ]
        },

      ]
    }
  }
}
</script>

<style scoped>
footer {
  background-color: var(--light-grey-color);
}

h5 {
  font-family: var(--heading-font);
  font-weight: 700;
  color: var(--dark-color);
  font-size: 1.2rem;
}

.social-links .btn {
  width: 40px;
  height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

a {
  transition: all 0.3s ease;
}

a:hover {
  color: var(--accent-color) !important;
}

.footer-bottom {
  background-color: #2b2d42;
  /* Modern dark */
  color: #edf2f4;
  /* Light gray */
}
</style>