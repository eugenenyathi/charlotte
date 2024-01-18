import useAuth from "./useAuth.js";

const useToken = () => {
  const { getAuthUser } = useAuth();
  const user = getAuthUser();

  return user.token;
};

export default useToken;
