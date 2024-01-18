import axios from "axios";
import { ref, computed } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import useAuth from "../composables/useAuth.js";
import useAxiosError from "../composables/useAxiosError.js";

const useSignup = () => {
	const axiosError = ref(null);
	const isLoading = ref(false);
	const store = useStore();
	const router = useRouter();
	const { setAuthUser } = useAuth();

	const studentID = computed(() => store.getters.getStudentID);

	const verifyIdentity = async (studentID, nationalID) => {
		try {
			isLoading.value = true;
			await axios.post("/validate", { studentID, nationalID });

			return true;
		} catch (err) {
			useAxiosError(err, axiosError, isLoading);
		}
	};

	const signup = async (password) => {
		try {
			isLoading.value = true;
			const { data: user } = await axios.post("/register", {
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

	return {
		axiosError,
		isLoading,
		verifyIdentity,
		signup,
	};
};

export default useSignup;
