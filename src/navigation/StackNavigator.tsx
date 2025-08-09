import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../screens/login_screen';
import Dashboard from '../screens/dashboard';

export type RootStackParamList = {
  Login: undefined;
  Dashboard: undefined;
  EducatorDashboard: undefined;
};

const Stack = createStackNavigator<RootStackParamList>();

const StackNavigator = () => {
  return (
    <Stack.Navigator
      id={undefined} // Add this line to satisfy the type requirement
      initialRouteName="Login"
      screenOptions={{ headerShown: false }}
    >
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="Dashboard" component={Dashboard} />
      <Stack.Screen name="EducatorDashboard" component={Dashboard} />
    </Stack.Navigator>
  );
};

export default StackNavigator;