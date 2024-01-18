import { createStore } from "vuex";

const store = createStore({
  state() {
    return {
      user: null,
      studentID: null,
      searchResults: [],
      cachedSearchResults: [],
      selectedStudents: [],
      selectedStudentsInfo: [],
      preferredRoommates: [],
      roommates: [],
    };
  },

  getters: {
    getUser(state) {
      return state.user;
    },
    getStudentID(state) {
      return state.studentID;
    },
    searchResults(state) {
      return state.searchResults;
    },
    cachedSearchResults(state) {
      return state.cachedSearchResults;
    },
    selectedStudents(state) {
      return state.selectedStudents;
    },
    selectedStudentsInfo(state) {
      return state.selectedStudentsInfo;
    },
    preferredRoommates(state) {
      return state.preferredRoommates;
    },
    roommates(state) {
      return state.roommates;
    },
  },

  mutations: {
    setUser(state, payload) {
      state.user = payload;
    },
    setStudentID(state, payload) {
      state.studentID = payload;
    },
    login(state, payload) {
      state.user = payload;
    },
    logout(state) {
      state.user = null;
    },
    addResults(state, payload) {
      state.searchResults = payload;
    },
    cacheResults(state, payload) {
      state.cachedSearchResults = payload;
    },
    clearResults(state) {
      state.searchResults = null;
    },
    addSelectedStudent(state, payload) {
      if (state.selectedStudents.length === 3) {
        state.selectedStudents.shift();
        state.selectedStudentsInfo.shift();
      }

      state.selectedStudents.push(payload);

      const selectedStudent = state.searchResults.find(
        (student) => student.id === payload
      );

      state.selectedStudentsInfo.push(selectedStudent);
    },
    removeSelectedStudent(state, payload) {
      state.selectedStudents = state.selectedStudents.filter(
        (studentNumber) => studentNumber !== payload
      );
      state.selectedStudentsInfo = state.selectedStudentsInfo.filter(
        (student) => student.id !== payload
      );
    },
    addPreferredRoommates(state, payload) {
      state.preferredRoommates = payload;
    },
    addRoommates(state, payload) {
      state.roommates = payload;
    },
  },

  actions: {
    setUser(context, payload) {
      context.commit("setUser", payload);
    },
    setStudentID(context, payload) {
      context.commit("setStudentID", payload);
    },
    login(context, payload) {
      context.commit("login", payload);
    },
    logout(context) {
      context.commit("logout");
    },
    addResults(context, payload) {
      context.commit("addResults", payload);
    },
    cacheResults(context, payload) {
      context.commit("cacheResults", payload);
    },
    clearResults(context) {
      context.commit("clearResults");
    },
    addSelectedStudent(context, payload) {
      context.commit("addSelectedStudent", payload);
    },
    removeSelectedStudent(context, payload) {
      context.commit("removeSelectedStudent", payload);
    },
    addPreferredRoommates(context, payload) {
      context.commit("addPreferredRoommates", payload);
    },
    addRoommates(context, payload) {
      context.commit("addRoommates", payload);
    },
  },
});

export default store;
