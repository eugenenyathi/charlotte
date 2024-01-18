import axios from "axios";
import useAxiosError from "./useAxiosError.js";
import useToken from "./useToken";

import { ref, computed } from "vue";
import { useStore } from "vuex";

const useSearch = () => {
  const store = useStore();
  const isLoading = ref(false);
  const axiosError = ref(null);
  const token = useToken();

  const cachedSearchResults = computed(() => store.getters.cachedSearchResults);

  const student = computed(() => store.getters.getUser);
  const { studentNumber } = student.value;

  // console.log(studentNumber);

  const searchStudent = async (studentNumberQuery) => {
    //first try to find if the queried potential student number
    //already exists from the previous search which is cachedSearchResults
    if (cachedSearchResults.value.length > 0) {
      let query = cachedSearchResults.value.filter((student) =>
        student.id.startsWith(studentNumberQuery)
      );

      if (query.length > 0) return store.dispatch("addResults", query);
      else queryDB(studentNumberQuery);
    } else {
      queryDB(studentNumberQuery);
    }
  };

  const queryDB = async (studentNumberQuery) => {
    try {
      isLoading.value = true;

      const { data: searchResults } = await axios.post(
        `/search/${studentNumberQuery}`,
        {
          studentID: studentNumber,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      isLoading.value = false;
      //update global state
      store.dispatch("addResults", searchResults);
      store.dispatch("cacheResults", searchResults);
    } catch (err) {
      useAxiosError(err, axiosError);
    }
  };

  return {
    isLoading,
    searchStudent,
  };
};

export default useSearch;
