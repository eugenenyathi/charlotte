//date-fns
import formatDistanceToNow from "date-fns/formatDistanceToNow";

const dateFns = (date) => {
	return formatDistanceToNow(new Date(date), {
		addSuffix: true,
	});
};

export default dateFns;
