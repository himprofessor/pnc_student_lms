import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

type FieldErrors = {
  email?: string;
  password?: string;
};

type Props = {
  navigation: any; // for navigation (React Navigation)
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

      const response = await axios.post('/login', {
        email,
        password,
      });

      const { token, user, role, dashboard_url } = response.data;

      await AsyncStorage.setItem('authToken', token);
      await AsyncStorage.setItem('user_data', JSON.stringify(user));
      await AsyncStorage.setItem('role', role);

      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      showSuccess('Login successful! Redirecting...');

      setTimeout(() => {
        if (dashboard_url) {
          navigation.replace(dashboard_url);
        } else {
          navigation.replace(role === 'teacher' ? 'EducatorDashboard' : 'Dashboard');
        }
      }, 1000);
    } catch (error: any) {
      if (error.response?.status === 422 && error.response?.data?.errors) {
        setFieldErrors(error.response.data.errors);
      } else if (error.response?.status === 401) {
        setFieldErrors({
          email: 'Invalid email',
          password: 'Invalid password',
        });
      } else {
        setFieldErrors({
          email: 'Login failed. Please try again.',
          password: 'Login failed. Please try again.',
        });
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      {/* Icon and welcome text */}
      <View style={styles.header}>
        <View style={styles.iconWrapper}>
          {/* Simple graduation cap SVG replacement */}
          <Text style={styles.icon}>🎓</Text>
        </View>
        <Text style={styles.title}>Welcome to PNC LeaveMS</Text>
        <Text style={styles.subtitle}>Sign in to manage your leave requests</Text>
      </View>

      {/* Login Form */}
      <View style={styles.form}>
        <View style={styles.field}>
          <Text style={styles.label}>Email</Text>
          <TextInput
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
            placeholder="Enter your email"
            style={[
              styles.input,
              fieldErrors.email ? styles.inputError : null,
            ]}
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
            style={[
              styles.input,
              fieldErrors.password ? styles.inputError : null,
            ]}
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

      {/* Success message */}
      {successMessage ? (
        <View style={styles.successBanner}>
          <Text style={styles.successText}>{successMessage}</Text>
        </View>
      ) : null}
    </View>
  );
};

export default LoginScreen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB', // bg-gray-50
    justifyContent: 'center',
    paddingHorizontal: 20,
  },
  header: {
    marginBottom: 40,
    alignItems: 'center',
  },
  iconWrapper: {
    backgroundColor: '#DBEAFE', // bg-blue-50
    borderRadius: 9999,
    padding: 12,
    marginBottom: 12,
  },
  icon: {
    fontSize: 32,
    color: '#2563EB', // text-blue-600
  },
  title: {
    fontSize: 20,
    fontWeight: '700',
    color: '#1F2937', // text-gray-800
    textAlign: 'center',
  },
  subtitle: {
    marginTop: 6,
    fontSize: 14,
    color: '#6B7280', // text-gray-500
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
    color: '#374151', // text-gray-700
  },
  input: {
    borderWidth: 1,
    borderColor: '#D1D5DB', // border-gray-300
    borderRadius: 10,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    backgroundColor: 'white',
  },
  inputError: {
    borderColor: '#EF4444', // border-red-500
  },
  errorText: {
    marginTop: 4,
    color: '#EF4444', // text-red-500
    fontSize: 12,
  },
  button: {
    backgroundColor: '#2563EB', // from-blue-600
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
    backgroundColor: '#3B82F6', // blue-500
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