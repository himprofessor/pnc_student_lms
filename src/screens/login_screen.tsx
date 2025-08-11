import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { StackNavigationProp } from '@react-navigation/stack';
import { RootStackParamList } from '../navigation/StackNavigator';

type FieldErrors = {
  email?: string;
  password?: string;
};

type Props = {
  navigation: StackNavigationProp<RootStackParamList, 'Login'>;
};

const LoginScreen: React.FC<Props> = ({ navigation }) => {
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [successMessage, setSuccessMessage] = useState<string>('');

  const showSuccess = (message: string) => {
    setSuccessMessage(message);
    setTimeout(() => setSuccessMessage(''), 3000);
  };

  const login = async () => {
    setFieldErrors({});
    setSuccessMessage('');
    setIsLoading(true);

    try {
      await AsyncStorage.multiRemove(['authToken', 'user_data', 'role']);

      const response = await axios.post('http://192.168.108.43:8080/api/login', {
        email: email.trim().toLowerCase(),
        password: password.trim(),
      });

      console.log('Login API response:', response.data);

      const { token, user, role, dashboard_url } = response.data;

      if (!token) {
        throw new Error('No token received from server.');
      }

      await AsyncStorage.setItem('authToken', token);
      await AsyncStorage.setItem('user_data', JSON.stringify(user));
      await AsyncStorage.setItem('role', role ?? '');
      
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      showSuccess('Login successful! Redirecting...');

      setTimeout(() => {
        let targetScreen: keyof RootStackParamList = 'Dashboard';
      
        if (role === 'teacher') {
          targetScreen = 'EducatorDashboard';
        }
      
        if (dashboard_url === '/educator-dashboard') {
          targetScreen = 'EducatorDashboard';
        }
      
        navigation.replace(targetScreen);
      }, 1000);
      
    } catch (error: any) {
      console.error('Login error:', error.response?.data || error.message);

      if (error.response?.status === 422 && error.response?.data?.errors) {
        setFieldErrors(error.response.data.errors);
      } else if (error.response?.status === 401) {
        setFieldErrors({
          email: 'Invalid credentials',
          password: 'Invalid credentials',
        });
      } else {
        setFieldErrors({
          email: error.message || 'Login failed. Please try again.',
          password: error.message || 'Login failed. Please try again.',
        });
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View style={styles.iconWrapper}>
          <Text style={styles.icon}>🎓</Text>
        </View>
        <Text style={styles.title}>Welcome to PNC LeaveMS</Text>
        <Text style={styles.subtitle}>Sign in to manage your leave requests</Text>
      </View>

      <View style={styles.form}>
        <View style={styles.field}>
          <Text style={styles.label}>Email</Text>
          <TextInput
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
            placeholder="Enter your email"
            style={[styles.input, fieldErrors.email ? styles.inputError : null]}
            editable={!isLoading}
          />
          {fieldErrors.email && (
            <Text style={styles.errorText}>{fieldErrors.email}</Text>
          )}
        </View>

        <View style={styles.field}>
          <Text style={styles.label}>Password</Text>
          <TextInput
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            placeholder="Enter your password"
            style={[styles.input, fieldErrors.password ? styles.inputError : null]}
            editable={!isLoading}
          />
          {fieldErrors.password && (
            <Text style={styles.errorText}>{fieldErrors.password}</Text>
          )}
        </View>

        <TouchableOpacity
          onPress={login}
          style={[styles.button, isLoading ? styles.buttonDisabled : null]}
          disabled={isLoading}
        >
          {isLoading ? (
            <>
              <ActivityIndicator color="white" />
              <Text style={styles.buttonText}>  Logging in...</Text>
            </>
          ) : (
            <Text style={styles.buttonText}>Login</Text>
          )}
        </TouchableOpacity>
      </View>

      {successMessage ? (
        <View style={styles.successBanner}>
          <Text style={styles.successText}>{successMessage}</Text>
        </View>
      ) : null}
    </View>
  );
};


// Your existing StyleSheet remains exactly the same
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
    justifyContent: 'center',
    paddingHorizontal: 20,
  },
  header: {
    marginBottom: 40,
    alignItems: 'center',
  },
  iconWrapper: {
    backgroundColor: '#DBEAFE',
    borderRadius: 9999,
    padding: 12,
    marginBottom: 12,
  },
  icon: {
    fontSize: 32,
    color: '#2563EB',
  },
  title: {
    fontSize: 20,
    fontWeight: '700',
    color: '#1F2937',
    textAlign: 'center',
  },
  subtitle: {
    marginTop: 6,
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
  },
  form: {
    width: '100%',
  },
  field: {
    marginBottom: 20,
  },
  label: {
    marginBottom: 6,
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
  },
  input: {
    borderWidth: 1,
    borderColor: '#D1D5DB',
    borderRadius: 10,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    backgroundColor: 'white',
  },
  inputError: {
    borderColor: '#EF4444',
  },
  errorText: {
    marginTop: 4,
    color: '#EF4444',
    fontSize: 12,
  },
  button: {
    backgroundColor: '#2563EB',
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
  },
  buttonDisabled: {
    opacity: 0.75,
  },
  buttonText: {
    color: 'white',
    fontWeight: '600',
    fontSize: 16,
  },
  successBanner: {
    position: 'absolute',
    top: 40,
    right: 20,
    backgroundColor: '#3B82F6',
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: 10,
    shadowColor: 'black',
    shadowOpacity: 0.2,
    shadowRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
  },
  successText: {
    color: 'white',
    fontWeight: '600',
  },
});

export default LoginScreen;