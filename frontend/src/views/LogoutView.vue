<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import useAuth from "../composables/useAuth.js";


const store = useStore();
const router = useRouter();
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
