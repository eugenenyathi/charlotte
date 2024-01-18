import Cookies from "js-cookie";

const useAuth = () => {
  const setAuthUser = (user) => {
    user = JSON.stringify(user);
    Cookies.set("mylsu", user, {
      expires: 14,
      path: "/",
      domain: "localhost",
      sameSite: "strict",
      secure: true,
    });
  };

  const getAuthUser = () => {
    const cookie = Cookies.get("mylsu");
    return cookie ? JSON.parse(cookie) : false;
  };

  const deleteAuthUser = () => {
    Cookies.remove("mylsu", {
      path: "/",
      domain: "localhost",
    });
  };

  return {
    setAuthUser,
    getAuthUser,
    deleteAuthUser,
  };
};

export default useAuth;
