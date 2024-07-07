const stRegex = (studentNumber) => {
	const regex = /^L0\d{6}[A-Z]{1}$/;

	if (regex.test(studentNumber)) return true;
	else return false;
};

export default stRegex;
