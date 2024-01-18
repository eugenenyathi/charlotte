import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { ref, computed } from "vue";
import axios from "axios";

//composables
import useAxiosError from "../composables/useAxiosError.js";
import useAuth from "../composables/useAuth.js";

const useLogin = () => {
  const store = useStore();
  const router = useRouter();
  const axiosError = ref(null);
  const isLoading = ref(false);
  const { setAuthUser } = useAuth();

  const login = async (studentID, password) => {
    try {
      isLoading.value = true;

      const { data: user } = await axios.post("/login", {
        studentID,
        password,
      });

      //update cookies
      setAuthUser(user);
      // console.log("User:", user);

      //update global state
      store.dispatch("login", user);

      //update studentID global state
      store.dispatch("setStudentID", studentID);

      //redirect to homepage
      router.push({
        name: "Dashboard",
      });

      // // object with path
      // router.push({ path: "/users/eduardo" });
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  return {
    axiosError,
    isLoading,
    login,
  };
};

export default useLogin;
