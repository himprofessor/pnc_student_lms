<template>
  <div class="register">
    <h2>Register</h2>
    <form @submit.prevent="registerUser">
      <input type="text" v-model="name" placeholder="Name" required />
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Register</button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      error: null,
    };
  },
  methods: {
    async registerUser() {
      try {
        const response = await this.$http.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
        });
        // Handle success (e.g., redirect or show a message)
        console.log('Registration successful:', response.data);
      } catch (err) {
        this.error = err.response.data.error || 'Registration failed';
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