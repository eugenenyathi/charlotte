const useAxiosError = (err, axiosError, isLoading) => {
	console.log(err);
	// console.log("There was an error :)");

	if (err.code === "ERR_NETWORK") {
		axiosError.value = "We could not reach our servers. Please try again";
	} else {
		const {
			response: { data },
		} = err;

		axiosError.value = data.error || data.err;
	}

	if (isLoading) isLoading.value = false;
};

export default useAxiosError;
