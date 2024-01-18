import axios from "axios";
import { ref, computed } from "vue";
import { useStore } from "vuex";
import useAxiosError from "../composables/useAxiosError.js";
import useToken from "./useToken.js";

const useRequest = () => {
  const isLoading = ref(false);
  const axiosError = ref(null);
  const token = useToken();
  const store = useStore();

  const student = computed(() => store.getters.getUser);
  const { studentNumber } = student.value;

  const selectedMates = computed(() => store.getters.selectedStudents);

  const createRequest = async () => {
    let responseData = [];

    const request = {
      requester: studentNumber,
    };

    //fabricate room mate object property
    let property = "roomie1";

    //get the last index of the string property
    let lastIndex = property.charAt(property.length - 1);

    //Loop through each selected student and create
    //and object property for each eg. roomie1 = 'L0202783T'

    selectedMates.value.forEach((roomie, index) => {
      //replace the number of string 'roomie1' with a dynamic one
      //result will be e.g. roomie2
      let facade = property.replace(lastIndex, index + 1);

      //  console.log("facade:", facade);

      //create the dynamic property
      request[`${facade}`] = roomie;

      //  console.log(request);
    });

    try {
      isLoading.value = true;

      const { data } = await axios.post("/request/create", request, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      isLoading.value = false;
      return data;
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }

    return responseData;
  };

  const sendResponse = async (response, requester = "") => {
    try {
      const { data: roomies } = await axios.patch(
        "/request/response",
        {
          studentID: studentNumber,
          requester,
          response,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response === "yes") store.dispatch("addPreferredRoommates", roomies);
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  const sendMultiResponse = async (response, requester) => {
    try {
      const { data: roomies } = await axios.patch(
        "/request/response",
        {
          studentID: studentNumber,
          requester,
          response,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response === "yes") store.dispatch("addPreferredRoommates", roomies);
    } catch (err) {
      useAxiosError(err, axiosError, isLoading);
    }
  };

  return {
    isLoading,
    axiosError,
    createRequest,
    sendResponse,
    sendMultiResponse,
  };
};

export default useRequest;
