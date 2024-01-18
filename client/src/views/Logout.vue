<script setup>
import axios from "axios";
import { computed } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import useAuth from "../composables/useAuth.js";
import useToken from "../composables/useToken.js";

const store = useStore();
const router = useRouter();
const token = useToken();
const { deleteAuthUser } = useAuth();

//get the student id
const student = computed(() => store.getters.getUser);
const { studentNumber } = student.value;

const logout = async (studentNumber) => {
  //delete cookie
  deleteAuthUser();
  //update global state
  store.dispatch("logout");
  //redirect to login
  router.push({ name: "Login" });
};

logout(studentNumber);
</script>
