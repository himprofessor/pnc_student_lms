import apiClient from './apiClient';

interface LoginResponse {
  token: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
  role: string;
  dashboard_url?: string;
}

export const login = async (email: string, password: string): Promise<LoginResponse> => {
  try {
    // First get CSRF token if using Sanctum
    await apiClient.get('/sanctum/csrf-cookie');
    
    const response = await apiClient.post<LoginResponse>('/login', {
      email: email.trim().toLowerCase(),
      password: password.trim()
    });
    
    return response.data;
  } catch (error: any) {
    console.error('Login error:', {
      status: error.response?.status,
      data: error.response?.data,
      message: error.message
    });
    
    if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
      throw new Error(errorMessages);
    }
    throw new Error(error.response?.data?.message || 'Login failed. Please try again.');
  }
};