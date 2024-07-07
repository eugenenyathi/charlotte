<template>
  <nav class="paginate-wrapper" v-if="pageNumbers.length > 0">
    <ul class="paginate-ul">
      <li class="list-item" v-for="page in pageNumbers" :key="page" @click="selectPage(page)"
        :class="{ isActive: isActiveId === page }">
        {{ page }}
      </li>
    </ul>
  </nav>
</template>
<script setup>
import usePagination from "@/composables/usePagination.js";

import { computed } from "vue";
import { useStore } from "vuex";

const store = useStore();
const pageNumbers = computed(() => store.getters.getPageNumbers);

const { pagination } = usePagination();

const isActiveId = computed(() => store.getters.getCurrentPage);

const selectPage = (pageNumber) => {
  pagination(pageNumber);
  store.dispatch("setCurrentPage", pageNumber);
};

</script>
