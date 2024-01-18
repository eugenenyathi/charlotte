import isEmpty from "../helpers/isEmpty.js";
import stRegex from "../helpers/stRegex.js";
const validator = require("is-my-date-valid");
const validateDate = validator({ format: "YYYY-MM-DD" });

const useValidator = () => {
	const validateStNumber = (studentNumber) => {
		if (isEmpty(studentNumber) || studentNumber.length !== 9) {
			return false;
		}
		if (!stRegex(studentNumber)) {
			return false;
		}

		return true;
	};

	const validatePassword = (password) => {
		if (isEmpty(password) || password.length < 8) {
			return false;
		}

		return true;
	};

	const validateNationalID = (nationalId) => {
		const natIdRegex = /^\d{2}-\d{6}[A-Z]{1}\d{2}$/;

		if (isEmpty(nationalId) || nationalId.length != 12) {
			return false;
		}

		if (!natIdRegex.test(nationalId)) {
			return false;
		}

		return true;
	};

	const validateDob = (dob) => {
		if (validateDate(dob)) {
			return true;
		}

		return false;
	};

	return {
		validateStNumber,
		validatePassword,
		validateNationalID,
		validateDob,
	};
};

export default useValidator;
