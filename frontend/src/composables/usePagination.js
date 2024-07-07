import { ref, computed } from "vue";
import { useStore } from "vuex";

const usePagination = () => {
  const store = useStore();
  const rowsPerPage = computed(() => store.getters.getRowsPerPage);

  const pageNumbers = ref([]);

  const pagination = (currentPage = 1) => {
    const routeData = computed(() => store.getters.getRouteData);
    const indexOfLastElement = currentPage * rowsPerPage.value;
    const indexOfFirstElement = indexOfLastElement - rowsPerPage.value;
    const paginatedData = routeData.value.slice(
      indexOfFirstElement,
      indexOfLastElement
    );

    store.dispatch("setPaginatedData", paginatedData);
  };

  const paginate = (arrLength) => {
    if (arrLength <= rowsPerPage.value) return [];

    for (let i = 1; i <= Math.ceil(arrLength / rowsPerPage.value); i++) {
      pageNumbers.value.push(i);
    }

    return pageNumbers.value;
  };

  return { pagination, paginate, rowsPerPage };
};

export default usePagination;
