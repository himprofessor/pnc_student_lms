import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Animated,
  Alert,
  ActivityIndicator
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { RootStackParamList } from '../navigation/StackNavigator';
import Feather from 'react-native-vector-icons/Feather';

interface UserData {
  id: number;
  name: string;
  email: string;
  role_id: number;
}

interface LeaveRequest {
  id: number;
  leave_type: string;
  from_date: string;
  to_date: string;
  status: 'pending' | 'approved' | 'rejected';
  created_at: string;
  approved_by?: string;
  reason?: string;
}

type LeaveRequestProps = {
  title: string;
  date: string;
  reason: string;
  status: string;
  statusColor: string;
  canCancel: boolean;
  onCancel?: () => void;
};

const LeaveRequestCard = ({
  title,
  date,
  reason,
  status,
  statusColor,
  canCancel,
  onCancel
}: LeaveRequestProps) => (
  <View style={styles.leaveCard}>
    <Feather name="file-text" size={24} color="#9CA3AF" />
    <View style={{ flex: 1, marginLeft: 12 }}>
      <Text style={styles.leaveTitle}>{title}</Text>
      <Text style={styles.leaveText}>{date}</Text>
      <Text style={styles.leaveText}>{reason}</Text>
    </View>
    <View style={{ alignItems: 'flex-end' }}>
      <Text style={[styles.status, { backgroundColor: `${statusColor}20`, color: statusColor }]}>
        {status}
      </Text>
      {canCancel && (
        <TouchableOpacity onPress={onCancel}>
          <Text style={styles.cancelBtn}>Cancel</Text>
        </TouchableOpacity>
      )}
    </View>
  </View>
);

const StatCard = ({ icon, color, bgColor, label, value }: {
  icon: string;
  color: string;
  bgColor: string;
  label: string;
  value: string;
}) => (
  <View style={styles.statCard}>
    <View style={[styles.iconContainer, { backgroundColor: bgColor }]}>
      <Feather name={icon} size={24} color={color} />
    </View>
    <View>
      <Text style={styles.statLabel}>{label}</Text>
      <Text style={styles.statValue}>{value}</Text>
    </View>
  </View>
);

const DashboardScreen = () => {
  const navigation = useNavigation<StackNavigationProp<RootStackParamList>>();
  const [user, setUser] = useState<UserData | null>(null);
  const [leaveRequests, setLeaveRequests] = useState<LeaveRequest[]>([]);
  const [pendingCount, setPendingCount] = useState<string>('0');
  const [approvedCount, setApprovedCount] = useState<string>('0');
  const [rejectedCount, setRejectedCount] = useState<string>('0');
  const [successMessage, setSuccessMessage] = useState<string>('');
  const [isLoggingOut, setIsLoggingOut] = useState(false);
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    const loadUserData = async () => {
      try {
        const userData = await AsyncStorage.getItem('user_data');
        if (userData) {
          setUser(JSON.parse(userData));
        }
      } catch (error) {
        console.error('Failed to load user data:', error);
      }
    };

    loadUserData();
    fetchLeaveRequests();
    
    if (successMessage) {
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 300,
        useNativeDriver: true,
      }).start();
      
      const timer = setTimeout(() => {
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }).start(() => setSuccessMessage(''));
      }, 3000);
      
      return () => clearTimeout(timer);
    }
  }, [successMessage]);

  const handleLogout = () => {
    Alert.alert(
      'Confirm Logout',
      'Are you sure you want to log out?',
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Log Out', onPress: logout, style: 'destructive' },
      ]
    );
  };

  const logout = async () => {
    setIsLoggingOut(true);
    try {
      const token = await AsyncStorage.getItem('authToken');
      
      try {
        await axios.post('http://10.193.247.163:8080/api/logout', {}, {
          headers: { Authorization: `Bearer ${token}` },
        });
      } catch (apiError) {
        console.log('Backend logout failed, proceeding with client-side logout', apiError);
      }

      await AsyncStorage.multiRemove(['user_data', 'authToken']);
      navigation.reset({
        index: 0,
        routes: [{ name: 'Login' }],
      });
    } catch (error) {
      console.error('Logout error:', error);
      await AsyncStorage.multiRemove(['user_data', 'authToken']);
      navigation.reset({
        index: 0,
        routes: [{ name: 'Login' }],
      });
    } finally {
      setIsLoggingOut(false);
    }
  };

  const fetchLeaveRequests = async () => {
    try {
      const token = await AsyncStorage.getItem('authToken');
      const response = await axios.get('http://10.193.247.163:8080/api/student/my-leaves', {
        headers: { Authorization: `Bearer ${token}` },
      });

      const requests: LeaveRequest[] = response.data.leaves.map((leave: any) => ({
        id: leave.id,
        leave_type: leave.leave_type,
        from_date: leave.from_date,
        to_date: leave.to_date,
        status: leave.status,
        created_at: leave.created_at,
        approved_by: leave.approved_by || 'N/A',
        reason: leave.reason || '',
      }));

      setLeaveRequests(requests);
      setPendingCount(requests.filter(r => r.status === 'pending').length.toString());
      setApprovedCount(requests.filter(r => r.status === 'approved').length.toString());
      setRejectedCount(requests.filter(r => r.status === 'rejected').length.toString());
    } catch (error) {
      console.error('Failed to fetch leave requests:', error);
      Alert.alert('Error', 'Failed to load leave requests');
    }
  };

  const cancelLeaveRequest = async (id: number) => {
    try {
      const token = await AsyncStorage.getItem('authToken');
      await axios.delete(
        `http://10.193.247.163:8080/api/student/leave-request/${id}`,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setSuccessMessage('Leave request cancelled successfully');
      await fetchLeaveRequests();
    } catch (err) {
      Alert.alert(
        'Error',
        `Failed to cancel leave: ${err.response?.data?.message || err.message}`
      );
      console.error('Cancel leave error:', err);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return '#FBBF24';
      case 'approved': return '#10B981';
      case 'rejected': return '#EF4444';
      default: return '#6B7280';
    }
  };

  const formatDateRange = (fromDate: string, toDate: string) => {
    const format = (dateStr: string) => {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    };
    return `${format(fromDate)} - ${format(toDate)}`;
  };

  return (
    <ScrollView style={styles.container}>
      {/* Success Alert */}
      {successMessage ? (
        <Animated.View style={[styles.successAlert, { opacity: fadeAnim }]}>
          <Feather name="check-circle" size={20} color="white" />
          <Text style={styles.successText}>{successMessage}</Text>
        </Animated.View>
      ) : null}

      {/* Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.headerTitle}>Welcome back, {user?.name || 'User'}</Text>
          <Text style={styles.headerSub}>Manage your leave requests and track their status</Text>
        </View>
        <TouchableOpacity 
          style={styles.logoutBtn}
          onPress={handleLogout}
          disabled={isLoggingOut}
        >
          {isLoggingOut ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <>
              <Feather name="log-out" size={16} color="#fff" />
              <Text style={styles.logoutText}>Logout</Text>
            </>
          )}
        </TouchableOpacity>
      </View>

      {/* Stats */}
      <View style={styles.statsRow}>
        <StatCard 
          icon="clock" 
          color="#FBBF24" 
          bgColor="#FEF3C7" 
          label="Pending" 
          value={pendingCount} 
        />
        <StatCard 
          icon="check" 
          color="#10B981" 
          bgColor="#D1FAE5" 
          label="Approved" 
          value={approvedCount} 
        />
        <StatCard 
          icon="x" 
          color="#EF4444" 
          bgColor="#FEE2E2" 
          label="Rejected" 
          value={rejectedCount} 
        />
        <StatCard 
          icon="file-text" 
          color="#3B82F6" 
          bgColor="#DBEAFE" 
          label="Total" 
          value={leaveRequests.length.toString()} 
        />
      </View>

      {/* New Leave Button */}
      <TouchableOpacity 
        style={styles.newLeaveBtn}
        onPress={() => navigation.navigate('RequestLeave')}
      >
        <Feather name="plus" size={16} color="#fff" />
        <Text style={styles.newLeaveText}>New Leave Request</Text>
      </TouchableOpacity>

      {/* Recent Requests */}
      <View style={styles.recentContainer}>
        <Text style={styles.recentTitle}>Recent Leave Requests</Text>
        <Text style={styles.recentSub}>Your latest leave requests and their status</Text>

        {leaveRequests.length > 0 ? (
          leaveRequests
            .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
            .slice(0, 5)
            .map(request => (
              <LeaveRequestCard
                key={request.id}
                title={request.leave_type}
                date={formatDateRange(request.from_date, request.to_date)}
                reason={request.reason}
                status={request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                statusColor={getStatusColor(request.status)}
                canCancel={request.status === 'pending'}
                onCancel={() => cancelLeaveRequest(request.id)}
              />
            ))
        ) : (
          <Text style={styles.emptyText}>No leave requests found</Text>
        )}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
    padding: 16,
  },
  successAlert: {
    position: 'absolute',
    top: 16,
    right: 16,
    backgroundColor: '#10B981',
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 8,
    zIndex: 100,
    elevation: 4,
  },
  successText: {
    color: 'white',
    marginLeft: 8,
    fontWeight: '500',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  headerSub: {
    fontSize: 12,
    color: '#6B7280',
  },
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'gray',
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 6,
  },
  logoutText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
    marginLeft: 4,
  },
  newLeaveBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#2563EB',
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 6,
    marginBottom: 20,
  },
  newLeaveText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
    marginLeft: 4,
  },
  statsRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
    marginBottom: 20,
  },
  statCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 12,
    borderRadius: 8,
    flex: 1,
    minWidth: '45%',
  },
  iconContainer: {
    padding: 8,
    borderRadius: 50,
    marginRight: 8,
  },
  statLabel: {
    fontSize: 12,
    color: '#6B7280',
    fontWeight: '600',
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  recentContainer: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
  },
  recentTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  recentSub: {
    fontSize: 12,
    color: '#6B7280',
    marginBottom: 10,
  },
  leaveCard: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  leaveTitle: {
    fontWeight: '600',
    color: '#1F2937',
  },
  leaveText: {
    fontSize: 12,
    color: '#6B7280',
  },
  status: {
    fontSize: 10,
    fontWeight: '600',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
  },
  cancelBtn: {
    fontSize: 10,
    fontWeight: '600',
    color: '#EF4444',
    marginTop: 4,
  },
  emptyText: {
    textAlign: 'center',
    color: '#9CA3AF',
    paddingVertical: 16,
  },
});

export default DashboardScreen;