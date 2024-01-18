import AlertFn from "../helpers/AlertFn.js";
import { watch } from "vue";

const watchFn = () => {
	const watchAxiosErr = (alert, axiosError) => {
		const { showAlert, removeAlert } = AlertFn(alert);

		watch(axiosError, (currentValue, oldValue) => {
			if (currentValue) {
				showAlert(true, currentValue, "danger");
				removeAlert();
			}

			axiosError.value = null;
		});
	};

	return {
		watchAxiosErr,
	};
};

export default watchFn;
