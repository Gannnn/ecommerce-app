<script setup lang="ts">
const year = new Date().getFullYear()

const links = [
  {
    heading: 'Shop',
    items: ['iPhone', 'Mac', 'iPad', 'AirPods', 'Apple Watch', 'Accessories'],
  },
  {
    heading: 'Account',
    items: ['Sign In', 'Create Account', 'Your Orders', 'Your Bag'],
  },
  {
    heading: 'Apple Store',
    items: ['Find a Store', 'Genius Bar', 'Today at Apple', 'Apple Camp'],
  },
  {
    heading: 'About Apple',
    items: ['Newsroom', 'Apple Leadership', 'Career Opportunities', 'Investors'],
  },
]
</script>

<template>
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-grid">
        <div v-for="col in links" :key="col.heading" class="footer-col">
          <h4 class="footer-heading">{{ col.heading }}</h4>
          <ul class="footer-list">
            <li v-for="item in col.items" :key="item">
              <a href="#" class="footer-link">{{ item }}</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-divider" />

      <div class="footer-bottom">
        <p class="footer-copy">
          Copyright &copy; {{ year }} Apple Store Demo. All rights reserved.
        </p>
        <div class="footer-legal-links">
          <a href="#" class="footer-legal-link">Privacy Policy</a>
          <a href="#" class="footer-legal-link">Terms of Use</a>
          <a href="#" class="footer-legal-link">Sales Policy</a>
          <a href="#" class="footer-legal-link">Legal</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background: var(--apple-light);
  border-top: 1px solid var(--apple-border);
  margin-top: 80px;
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 48px 24px 32px;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
  margin-bottom: 40px;
}

.footer-heading {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--apple-dark);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0 0 14px;
}

.footer-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-link {
  font-size: 0.8125rem;
  color: var(--apple-mid);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-link:hover {
  color: var(--apple-dark);
}

.footer-divider {
  border: none;
  border-top: 1px solid var(--apple-border);
  margin-bottom: 24px;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.footer-copy {
  font-size: 0.75rem;
  color: var(--apple-mid);
  margin: 0;
}

.footer-legal-links {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.footer-legal-link {
  font-size: 0.75rem;
  color: var(--apple-mid);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-legal-link:hover {
  color: var(--apple-dark);
}

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 28px;
  }

  .footer-bottom {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 480px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>
