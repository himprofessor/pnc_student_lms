<template>
  <div class="login">
    <h2>Login</h2>
    <form @submit.prevent="loginUser">
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Login</button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      error: null,
    };
  },
  methods: {
    async loginUser() {
      try {
        const response = await this.$http.post('/api/login', {
          email: this.email,
          password: this.password,
        });
        // Handle success (e.g., save token and redirect)
        console.log('Login successful:', response.data);
      } catch (err) {
        this.error = err.response.data.error || 'Login failed';
      }
    },
  },
};
</script>

<style scoped>
.error {
  color: red;
}
</style>