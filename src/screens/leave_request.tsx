import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  ScrollView,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
  ActivityIndicator,
  Image,
  Platform,
} from "react-native";
import { Picker } from "@react-native-picker/picker";
import axios from "axios";
import * as DocumentPicker from "expo-document-picker";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { useNavigation } from "@react-navigation/native";
import { StackNavigationProp } from "@react-navigation/stack";
import { RootStackParamList } from "../navigation/StackNavigator";
import Feather from "react-native-vector-icons/Feather";
import DateTimePicker from "@react-native-community/datetimepicker";

interface LeaveType {
  id: number;
  name: string;
}

const RequestLeaveScreen = () => {
  const navigation = useNavigation<StackNavigationProp<RootStackParamList>>();
  const [leaveTypes, setLeaveTypes] = useState<LeaveType[]>([]);
  const [loading, setLoading] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");
  const [errorMessage, setErrorMessage] = useState("");
  const [totalDays, setTotalDays] = useState<number | null>(null);
  const [showFromDatePicker, setShowFromDatePicker] = useState(false);
  const [showToDatePicker, setShowToDatePicker] = useState(false);

  const [form, setForm] = useState({
    leave_type_id: "",
    reason: "",
    from_date: "",
    to_date: "",
    contact_info: "",
    supporting_documents: null as any,
  });

  const [fieldErrors, setFieldErrors] = useState({
    leave_type_id: "",
    reason: "",
    from_date: "",
    to_date: "",
    contact_info: "",
    supporting_documents: "",
  });

  // Get today's date in YYYY-MM-DD format
  const getToday = () => {
    const date = new Date();
    return date.toISOString().split("T")[0];
  };

  useEffect(() => {
    fetchLeaveTypes();
  }, []);

  const fetchLeaveTypes = async () => {
    try {
      const token = await AsyncStorage.getItem("authToken");
      const response = await axios.get(
        "http://192.168.108.43:8080/api/leave-types",
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      setLeaveTypes(response.data.data);
    } catch (error) {
      console.error("Error fetching leave types:", error);
      showError(
        error.response?.data?.message ||
          "Failed to load leave types. Please try again."
      );
    }
  };

  const calculateDays = () => {
    if (form.from_date && form.to_date) {
      const from = new Date(form.from_date);
      const to = new Date(form.to_date);

      // Ensure to_date is not before from_date
      if (from > to) {
        setForm({ ...form, to_date: form.from_date });
        return;
      }

      // Calculate difference in days (inclusive of both dates)
      const diffTime = Math.abs(to.getTime() - from.getTime());
      const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
      setTotalDays(diffDays);
    } else {
      setTotalDays(null);
    }
  };

  const handleFileUpload = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: [
          "application/pdf",
          "image/*",
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        ],
      });

      if (!result.canceled) {
        setForm({ ...form, supporting_documents: result.assets[0] });
      }
    } catch (error) {
      console.error("Error picking document:", error);
    }
  };

  const showSuccess = (message: string) => {
    setSuccessMessage(message);
    setTimeout(() => {
      setSuccessMessage("");
    }, 5000);
  };

  const showError = (message: string) => {
    setErrorMessage(message);
    setTimeout(() => {
      setErrorMessage("");
    }, 5000);
  };

  const clearFieldErrors = () => {
    setFieldErrors({
      leave_type_id: "",
      reason: "",
      from_date: "",
      to_date: "",
      contact_info: "",
      supporting_documents: "",
    });
  };

  const submitLeaveRequest = async () => {
    setLoading(true);
    setSuccessMessage("");
    setErrorMessage("");
    clearFieldErrors();

    const authToken = await AsyncStorage.getItem("authToken");
    if (!authToken) {
      showError("Authentication token not found. Please log in again.");
      setLoading(false);
      navigation.navigate("Login");
      return;
    }

    const formData = new FormData();
    formData.append("leave_type_id", form.leave_type_id);
    formData.append("reason", form.reason);
    formData.append("from_date", form.from_date);
    formData.append("to_date", form.to_date);
    if (form.contact_info) {
      formData.append("contact_info", form.contact_info);
    }
    if (form.supporting_documents) {
      formData.append("supporting_documents", {
        uri: form.supporting_documents.uri,
        type: form.supporting_documents.mimeType,
        name: form.supporting_documents.name,
      } as any);
    }

    try {
      const response = await axios.post(
        "http://192.168.108.43:8080/api/student/request-leave",
        formData,
        {
          headers: {
            Authorization: `Bearer ${authToken}`,
            "Content-Type": "multipart/form-data",
          },
        }
      );

      showSuccess("Leave request submitted successfully!");

      setTimeout(() => {
        navigation.navigate("Dashboard");
      }, 1500);
    } catch (error: any) {
      console.error("Error submitting leave request:", error);

      if (error.response) {
        const status = error.response.status;
        const responseData = error.response.data;

        if (status === 422 && responseData.errors) {
          // Map errors to individual fields
          const newErrors = { ...fieldErrors };
          for (const key in responseData.errors) {
            const fieldName = key.replace(
              /\./g,
              "_"
            ) as keyof typeof fieldErrors;
            if (fieldErrors[fieldName] !== undefined) {
              newErrors[fieldName] = responseData.errors[key][0];
            }
          }
          setFieldErrors(newErrors);
        } else if (status === 401 || status === 403) {
          showError(
            responseData.message || "Unauthorized. Please log in again."
          );
          navigation.navigate("Login");
        } else {
          showError(
            responseData.message ||
              `Error ${status}: ${
                error.response.statusText || "Something went wrong."
              }`
          );
        }
      } else if (error.request) {
        showError(
          "No response from server. Please check your network or try again later."
        );
      } else {
        showError("An unknown error occurred during the request.");
      }
    } finally {
      setLoading(false);
    }
  };

  const resetForm = () => {
    setForm({
      leave_type_id: "",
      reason: "",
      from_date: "",
      to_date: "",
      contact_info: "",
      supporting_documents: null,
    });
    setTotalDays(null);
    clearFieldErrors();
    setErrorMessage("");
    setSuccessMessage("");
  };

  // DateTimePicker handlers
  const onFromDateChange = (event: any, selectedDate?: Date) => {
    setShowFromDatePicker(Platform.OS === "ios"); // Keep picker open on iOS
    if (event.type === "dismissed") {
      setShowFromDatePicker(false);
      return;
    }
    const currentDate = selectedDate || new Date(form.from_date || getToday());
    const formattedDate = currentDate.toISOString().split("T")[0];
    setForm({ ...form, from_date: formattedDate });
    setShowFromDatePicker(false);
    calculateDays();
  };

  const onToDateChange = (event: any, selectedDate?: Date) => {
    setShowToDatePicker(Platform.OS === "ios"); // Keep picker open on iOS
    if (event.type === "dismissed") {
      setShowToDatePicker(false);
      return;
    }
    const currentDate = selectedDate || new Date(form.to_date || getToday());
    const formattedDate = currentDate.toISOString().split("T")[0];
    setForm({ ...form, to_date: formattedDate });
    setShowToDatePicker(false);
    calculateDays();
  };

  const showFromPicker = () => {
    setShowFromDatePicker(true);
  };

  const showToPicker = () => {
    setShowToDatePicker(true);
  };

  return (
    <ScrollView style={styles.container}>
      {/* Success Alert */}
      {successMessage ? (
        <View style={styles.successAlert}>
          <Feather name="check-circle" size={20} color="white" />
          <Text style={styles.successText}>{successMessage}</Text>
        </View>
      ) : null}

      {/* Error Alert */}
      {errorMessage ? (
        <View style={styles.errorAlert}>
          <Text style={styles.errorText}>{errorMessage}</Text>
          <TouchableOpacity onPress={() => setErrorMessage("")}>
            <Feather name="x" size={20} color="#EF4444" />
          </TouchableOpacity>
        </View>
      ) : null}

      <View style={styles.header}>
        <Text style={styles.headerTitle}>Submit Leave Request</Text>
        <Text style={styles.headerSub}>
          Fill out the form below to request leave from your studies
        </Text>
      </View>

      <View style={styles.formContainer}>
        {/* Leave Type */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Leave Type *</Text>
          <View
            style={[
              styles.pickerContainer,
              fieldErrors.leave_type_id ? styles.errorBorder : null,
            ]}
          >
            <Picker
              selectedValue={form.leave_type_id}
              onValueChange={(itemValue) =>
                setForm({ ...form, leave_type_id: itemValue })
              }
            >
              <Picker.Item label="Select leave type" value="" />
              {leaveTypes.map((type) => (
                <Picker.Item key={type.id} label={type.name} value={type.id} />
              ))}
            </Picker>
          </View>
          {fieldErrors.leave_type_id ? (
            <Text style={styles.errorText}>{fieldErrors.leave_type_id}</Text>
          ) : null}
        </View>

        {/* Date Range */}
        <View style={styles.dateRow}>
          <View style={styles.dateInput}>
            <Text style={styles.label}>Start Date *</Text>
            <TouchableOpacity
              style={[
                styles.input,
                fieldErrors.from_date ? styles.errorBorder : null,
              ]}
              onPress={showFromPicker}
            >
              <Text style={styles.inputText}>
                {form.from_date
                  ? new Date(form.from_date).toLocaleDateString()
                  : "Start date"}
              </Text>
            </TouchableOpacity>
            {showFromDatePicker && (
              <DateTimePicker
                value={form.from_date ? new Date(form.from_date) : new Date()}
                mode="date"
                display="default"
                onChange={onFromDateChange}
                minimumDate={new Date(getToday())}
              />
            )}
            {fieldErrors.from_date ? (
              <Text style={styles.errorText}>{fieldErrors.from_date}</Text>
            ) : null}
          </View>

          <View style={styles.dateInput}>
            <Text style={styles.label}>End Date *</Text>
            <TouchableOpacity
              style={[
                styles.input,
                fieldErrors.to_date ? styles.errorBorder : null,
              ]}
              onPress={showToPicker}
            >
              <Text style={styles.inputText}>
                {form.to_date
                  ? new Date(form.to_date).toLocaleDateString()
                  : "End date"}
              </Text>
            </TouchableOpacity>
            {showToDatePicker && (
              <DateTimePicker
                value={form.to_date ? new Date(form.to_date) : new Date()}
                mode="date"
                display="default"
                onChange={onToDateChange}
                minimumDate={
                  form.from_date ? new Date(form.from_date) : new Date()
                }
              />
            )}
            {fieldErrors.to_date ? (
              <Text style={styles.errorText}>{fieldErrors.to_date}</Text>
            ) : null}
          </View>
        </View>

        {totalDays !== null && (
          <Text style={styles.daysText}>
            Total Leave Days: {totalDays} day{totalDays !== 1 ? "s" : ""}
          </Text>
        )}

        {/* Reason */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Reason for Leave *</Text>
          <TextInput
            style={[
              styles.textArea,
              fieldErrors.reason ? styles.errorBorder : null,
            ]}
            value={form.reason}
            onChangeText={(text) => setForm({ ...form, reason: text })}
            placeholder="Please provide a detailed reason for your leave request..."
            multiline
            numberOfLines={4}
          />
          {fieldErrors.reason ? (
            <Text style={styles.errorText}>{fieldErrors.reason}</Text>
          ) : null}
        </View>

        {/* Contact Info */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Contact Information During Leave</Text>
          <TextInput
            style={[
              styles.input,
              fieldErrors.contact_info ? styles.errorBorder : null,
            ]}
            value={form.contact_info}
            onChangeText={(text) => setForm({ ...form, contact_info: text })}
            placeholder="Phone number or email"
            keyboardType="email-address"
          />
          {fieldErrors.contact_info ? (
            <Text style={styles.errorText}>{fieldErrors.contact_info}</Text>
          ) : null}
        </View>

        {/* Supporting Documents */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Supporting Documents</Text>
          <TouchableOpacity
            style={styles.fileUpload}
            onPress={handleFileUpload}
          >
            <Feather name="upload" size={24} color="#6B7280" />
            <Text style={styles.fileUploadText}>
              {form.supporting_documents
                ? form.supporting_documents.name
                : "Tap to upload files (PDF, DOC, JPG, PNG up to 2MB)"}
            </Text>
          </TouchableOpacity>
          {fieldErrors.supporting_documents ? (
            <Text style={styles.errorText}>
              {fieldErrors.supporting_documents}
            </Text>
          ) : null}
        </View>

        {/* Form Actions */}
        <View style={styles.actions}>
          <TouchableOpacity style={styles.cancelButton} onPress={resetForm}>
            <Text style={styles.cancelButtonText}>Cancel</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.submitButton}
            onPress={submitLeaveRequest}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <Text style={styles.submitButtonText}>Submit Request</Text>
            )}
          </TouchableOpacity>
        </View>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9FAFB",
    padding: 16,
  },
  header: {
    marginBottom: 20,
    marginTop: 10,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#2563EB",
    marginBottom: 4,
  },
  headerSub: {
    fontSize: 14,
    color: "#6B7280",
  },
  formContainer: {
    backgroundColor: "white",
    borderRadius: 8,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  inputGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 14,
    fontWeight: "500",
    color: "#374151",
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: "#D1D5DB",
    borderRadius: 6,
    padding: 12,
    fontSize: 16,
    backgroundColor: "white",
    justifyContent: "center",
  },
  inputText: {
    fontSize: 16,
    color: "#374151",
  },
  textArea: {
    borderWidth: 1,
    borderColor: "#D1D5DB",
    borderRadius: 6,
    padding: 12,
    fontSize: 16,
    height: 120,
    textAlignVertical: "top",
    backgroundColor: "white",
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: "#D1D5DB",
    borderRadius: 6,
    backgroundColor: "white",
  },
  dateRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 16,
  },
  dateInput: {
    width: "48%",
  },
  daysText: {
    fontSize: 14,
    color: "#6B7280",
    marginBottom: 16,
  },
  fileUpload: {
    borderWidth: 1,
    borderColor: "#D1D5DB",
    borderRadius: 6,
    padding: 16,
    alignItems: "center",
    justifyContent: "center",
    borderStyle: "dashed",
  },
  fileUploadText: {
    marginTop: 8,
    textAlign: "center",
    color: "#6B7280",
  },
  actions: {
    flexDirection: "row",
    justifyContent: "flex-end",
    marginTop: 16,
  },
  cancelButton: {
    backgroundColor: "#E5E7EB",
    borderRadius: 6,
    padding: 12,
    marginRight: 12,
    minWidth: 100,
    alignItems: "center",
  },
  cancelButtonText: {
    color: "#111827",
    fontWeight: "500",
  },
  submitButton: {
    backgroundColor: "#2563EB",
    borderRadius: 6,
    padding: 12,
    minWidth: 150,
    alignItems: "center",
  },
  submitButtonText: {
    color: "white",
    fontWeight: "500",
  },
  successAlert: {
    position: "absolute",
    top: 16,
    right: 16,
    backgroundColor: "#10B981",
    flexDirection: "row",
    alignItems: "center",
    padding: 12,
    borderRadius: 8,
    zIndex: 100,
    elevation: 4,
  },
  successText: {
    color: "white",
    marginLeft: 8,
    fontWeight: "500",
  },
  errorAlert: {
    backgroundColor: "#FEE2E2",
    borderColor: "#FECACA",
    borderWidth: 1,
    borderRadius: 6,
    padding: 12,
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 16,
  },
  errorText: {
    color: "#EF4444",
    fontSize: 14,
    marginTop: 4,
  },
  errorBorder: {
    borderColor: "#EF4444",
  },
});

export default RequestLeaveScreen;
