const AlertFn = (alert) => {
	const showAlert = (show = false, msg, type) => {
		alert.show = show;
		alert.msg = msg;
		alert.type = type;
	};

	const removeAlert = () => {
		const clear = setTimeout(() => {
			showAlert();
		}, 3000);
		return () => clearTimeout(clear);
	};

	return { showAlert, removeAlert };
};

export default AlertFn;
