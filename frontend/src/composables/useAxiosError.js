const useAxiosError = (err, axiosError, isLoading) => {
  if (err.code === 'ERR_NETWORK') {
    axiosError.value = 'We could not reach our servers. Please try again'
  } else {
    const {
      response: { data }
    } = err

    axiosError.value = data.error || data.err || data.message
  }

  if (isLoading.value) isLoading.value = false
}

export default useAxiosError
