import AsyncStorage from '@react-native-async-storage/async-storage';
import apiClient from './apiClient';
import csrfClient from './csrfClient';

export const login = async (email: string, password: string) => {
  try {
    await csrfClient.get('/sanctum/csrf-cookie');

    const response = await apiClient.post('/login', {
      email: email.trim(),
      password: password.trim(),
    });

    const token = response.data?.token;

    if (token) {
      await AsyncStorage.setItem('authToken', token);
    } else {
      // Only call removeItem if token is missing
      await AsyncStorage.removeItem('authToken');
      throw new Error('Login failed: No token received');
    }

    return response.data;
  } catch (error) {
    await AsyncStorage.removeItem('authToken');
    console.error('Login error:', error);
    throw error;
  }
};
