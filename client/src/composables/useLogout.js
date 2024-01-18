import axios from "axios";
import { computed } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import useAuth from "./useAuth.js";
import useToken from "./useToken.js";

const useLogout = () => {
	const token = useToken();
	const store = useStore();
	const router = useRouter();
	const { deleteAuthUser } = useAuth();

	const logout = async () => {
		//logout from server
		await axios.post(
			"/logout",
			{},
			{
				headers: {
					Authorization: `Bearer ${token}`,
				},
			}
		);
		//delete cookie
		deleteAuthUser();
		//update global state
		store.dispatch("logout");
		//redirect to login
		router.push({ name: "Login" });
	};

	return { logout };
};

export default useLogout;
