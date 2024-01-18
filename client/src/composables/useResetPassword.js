import axios from "axios";
import { ref, computed } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import useAuth from "../composables/useAuth.js";
import useAxiosError from "../composables/useAxiosError.js";
import useToken from "./useToken";

const useResetPassword = (error) => {
  const isLoading = ref(false);
  const axiosError = ref(null);
  const store = useStore();
  const router = useRouter();
  const { setAuthUser } = useAuth();
  const token = useToken();

  const studentID = computed(() => store.getters.getStudentID);

  const verifyIdentity = async (studentID, nationalID, dob) => {
    try {
      isLoading.value = true;
      const { data: user } = await axios.post("/validate", {
        studentID,
        nationalID,
        dob,
      });

      return true;
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  const resetPwd = async (password) => {
    try {
      isLoading.value = true;

      const { data: user } = await axios.post("/reset_password", {
        studentID: studentID.value,
        password,
      });

      //update global state
      store.dispatch("login", user);
      //update cookies
      setAuthUser(user);
      //redirect to home
      router.push({ name: "Dashboard" });
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  const updatePassword = async (password) => {
    const student = computed(() => store.getters.getUser);
    const { studentNumber } = student.value;

    try {
      isLoading.value = true;

      await axios.patch(
        "/password/update",
        {
          studentID: studentNumber,
          password,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      // router.push({ name: "Dashboard" });
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  return {
    axiosError,
    isLoading,
    verifyIdentity,
    resetPwd,
    updatePassword,
  };
};

export default useResetPassword;
