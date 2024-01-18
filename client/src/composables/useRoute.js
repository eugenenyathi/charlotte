import useAuth from "./useAuth.js";

const useRoute = () => {
  const { getAuthUser } = useAuth();

  const protectHomeRoute = (to, from, next) => {
    let isAuthenticated = getAuthUser();
    if (isAuthenticated) next();
    else next("/login");
  };

  const redirectRoute = (to, from, next) => {
    //  console.log("l came here first");
    let isAuthenticated = getAuthUser();
    if (isAuthenticated) next("/");
    else next();
  };

  const redirectUser = (to, from, next) => {
    let isAuthenticated = getAuthUser();
    if (isAuthenticated) next();
    else next("/login");
  };

  return {
    protectHomeRoute,
    redirectRoute,
    redirectUser,
  };
};

export default useRoute;
