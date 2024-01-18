const clientWidth = () => {
  let width;

  window.addEventListener("change", (e) => {
    width = window.innerWidth;
  });
  return width;
};

export default clientWidth;
