import { createStore } from 'vuex'

const store = createStore({
  state() {
    return {
      user: null,
      studentID: null,
      rows: 5,
      pageNumbers: [],
      currentPage: 1,
      routeData: {
        data: [],
        paginatedData: []
      },
      searchResults: [],
      cachedSearchResults: [],
      selectedStudents: [],
      selectedStudentsInfo: [],
      preferredRoommates: [],
      confirmRoommates: {},
      multiConfirmRoommates: {},
      roommates: [],
      editingSelection: false,
      cachedProfile: [],
      cachedRoommatePreference: {},
      cachedResidence: [],
      cachedRequestStatus: false,
      cachedReminderInfo: {},
      cachedDynamicReminder: 'The system defaults to randomized roommates if data is insufficient.'
    }
  },

  getters: {
    getUser(state) {
      return state.user
    },
    getStudentID(state) {
      return state.studentID
    },
    getRowsPerPage(state) {
      return state.rows
    },
    getPageNumbers(state) {
      return state.pageNumbers
    },
    getCurrentPage(state) {
      return state.currentPage
    },
    getRouteData(state) {
      return state.routeData.data
    },
    getPaginatedData(state) {
      return state.routeData.paginatedData
    },
    getEditingSelection(state) {
      return state.editingSelection
    },
    searchResults(state) {
      return state.searchResults
    },
    cachedSearchResults(state) {
      return state.cachedSearchResults
    },
    selectedStudents(state) {
      return state.selectedStudents
    },
    selectedStudentsInfo(state) {
      return state.selectedStudentsInfo
    },
    preferredRoommates(state) {
      return state.preferredRoommates
    },
    confirmRoommates(state) {
      return state.confirmRoommates
    },
    multiConfirmRoommates(state) {
      return state.multiConfirmRoommates
    },
    roommates(state) {
      return state.roommates
    },
    cachedProfile(state) {
      return state.cachedProfile
    },
    cachedRoommatePreference(state) {
      return state.cachedRoommatePreference
    },
    cachedResidence(state) {
      return state.cachedResidence
    },
    cachedRequestStatus(state) {
      return state.cachedRequestStatus
    },
    cachedReminderInfo(state) {
      return state.cachedReminderInfo
    },
    cachedDynamicReminder(state) {
      return state.cachedDynamicReminder
    }
  },

  mutations: {
    setUser(state, payload) {
      state.user = payload
    },
    setStudentID(state, payload) {
      state.studentID = payload
    },
    login(state, payload) {
      state.user = payload
    },
    logout(state) {
      state.user = null
      state.studentID = null
      state.editingSelection = false
      state.searchResults = []
      state.routeData.data = []
      state.routeData.paginatedData = []
      state.selectedStudents = []
      state.selectedStudentsInfo = []
      state.preferredRoommates = []
      state.confirmRoommates = {}
      state.multiConfirmRoommates = {}
      state.roommates = []
      state.cachedProfile = []
      state.cachedRoommatePreference = {}
      state.cachedResidence = []
      state.cachedReminderInfo = {}
    },
    setPageNumbers(state, payload) {
      state.pageNumbers = payload
    },
    setCurrentPage(state, payload) {
      state.currentPage = payload
    },
    setRouteData(state, payload) {
      state.routeData.data = payload
    },
    setPaginatedData(state, payload) {
      state.routeData.paginatedData = payload
    },
    flushRouteData(state) {
      state.routeData = {
        data: [],
        paginatedData: []
      }
    },
    addResults(state, payload) {
      state.searchResults = payload
    },
    cacheResults(state, payload) {
      state.cachedSearchResults = payload
    },
    clearResults(state) {
      state.searchResults = null
    },
    addSelectedStudent(state, payload) {
      if (state.selectedStudents.length === 3) {
        state.selectedStudents.shift()
        state.selectedStudentsInfo.shift()
      }

      state.selectedStudents.push(payload.student_id)

      let selectedStudent = {}

      switch (payload.context) {
        case 'search':
          selectedStudent = state.searchResults.find((student) => student.id === payload.student_id)

          state.searchResults = state.searchResults.filter(
            (student) => student.id !== payload.student_id
          )
          break
        case 'find':
          selectedStudent = state.routeData.paginatedData.find(
            (student) => student.id === payload.student_id
          )

          state.routeData.paginatedData = state.routeData.paginatedData.filter(
            (student) => student.id !== payload.student_id
          )
          break
      }

      state.selectedStudentsInfo.push(selectedStudent)
    },
    removeSelectedStudent(state, payload) {
      let selectedStudent = state.selectedStudentsInfo.find((student) => student.id === payload)

      if (state.searchResults != null) {
        state.searchResults.push(selectedStudent)
      } else {
        state.routeData.paginatedData.push(selectedStudent)
      }

      state.selectedStudents = state.selectedStudents.filter(
        (studentNumber) => studentNumber !== payload
      )
      state.selectedStudentsInfo = state.selectedStudentsInfo.filter(
        (student) => student.id !== payload
      )
    },
    addPreferredRoommates(state, payload) {
      state.preferredRoommates = payload
    },
    editSelection(state) {
      //store the student id's
      state.selectedStudents = state.preferredRoommates.map((roommate) => roommate.id)
      state.selectedStudentsInfo = state.preferredRoommates.map((roommate) => {
        return {
          id: roommate.id,
          fullName: roommate.fullName,
          program: roommate.program
        }
      })
      state.editingSelection = true
    },
    confirmRoommates(state, payload) {
      state.confirmRoommates = payload
    },
    multiConfirmRoommates(state, payload) {
      state.multiConfirmRoommates = payload
    },
    addRoommates(state, payload) {
      state.roommates = payload
    },
    cacheResidence(state, payload) {
      state.cachedResidence = payload
    },
    cacheProfile(state, payload) {
      state.cachedProfile = payload
    },
    cacheRoommatePreference(state, payload) {
      state.cachedRoommatePreference = payload
    },
    updatePreference(state, payload) {
      switch (payload.questionId) {
        case 1:
          state.cachedRoommatePreference.question_1 = payload.response
          break
        case 2:
          state.cachedRoommatePreference.question_2 = payload.response
          break
      }
    },
    cacheRequestStatus(state, payload) {
      state.cachedRequestStatus = payload
    },
    cacheReminderInfo(state, payload) {
      state.cachedReminderInfo = payload
    },
    cacheDynamicReminder(state, payload) {
      state.cachedDynamicReminder = payload
      // state.cacheDynamicReminder.push(payload)
    }
  },

  actions: {
    setUser(context, payload) {
      context.commit('setUser', payload)
    },
    setStudentID(context, payload) {
      context.commit('setStudentID', payload)
    },
    login(context, payload) {
      context.commit('login', payload)
    },
    logout(context) {
      context.commit('logout')
    },
    setPageNumbers(context, payload) {
      context.commit('setPageNumbers', payload)
    },
    setCurrentPage(context, payload) {
      context.commit('setCurrentPage', payload)
    },
    setRouteData(context, payload) {
      context.commit('setRouteData', payload)
    },
    setPaginatedData(context, payload) {
      context.commit('setPaginatedData', payload)
    },
    flushRouteData(context) {
      context.commit('flushRouteData')
    },
    addResults(context, payload) {
      context.commit('addResults', payload)
    },
    cacheResults(context, payload) {
      context.commit('cacheResults', payload)
    },
    clearResults(context) {
      context.commit('clearResults')
    },
    addSelectedStudent(context, payload) {
      context.commit('addSelectedStudent', payload)
    },
    removeSelectedStudent(context, payload) {
      context.commit('removeSelectedStudent', payload)
    },
    addPreferredRoommates(context, payload) {
      context.commit('addPreferredRoommates', payload)
    },
    editSelection(context) {
      context.commit('editSelection')
    },
    confirmRoommates(context, payload) {
      context.commit('confirmRoommates', payload)
    },
    multiConfirmRoommates(context, payload) {
      context.commit('multiConfirmRoommates', payload)
    },
    addRoommates(context, payload) {
      context.commit('addRoommates', payload)
    },
    cacheProfile(context, payload) {
      context.commit('cacheProfile', payload)
    },
    cacheResidence(context, payload) {
      context.commit('cacheResidence', payload)
    },
    cacheRoommatePreference(context, payload) {
      context.commit('cacheRoommatePreference', payload)
    },
    updatePreference(context, payload) {
      context.commit('updatePreference', payload)
    },
    cacheRequestStatus(context, payload) {
      context.commit('cacheRequestStatus', payload)
    },
    cacheReminderInfo(context, payload) {
      context.commit('cacheReminderInfo', payload)
    },
    cacheDynamicReminder(context, payload) {
      context.commit('cacheDynamicReminder', payload)
    }
  }
})

export default store
