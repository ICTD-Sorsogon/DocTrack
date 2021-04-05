import { formatDistanceStrict } from 'date-fns';

export function groupBy (xs, key) {
	return xs.reduce(function(rv, x) {
		(rv[x[key]] = rv[x[key]] || []).push(x);
		return rv;
	}, {})
}

export function pluck(arr, key) {
	return arr.map(o=>o[key])
}

export function getRecordSpeed(arr, operator) {
	return arr.reduce((speed, record) => {
		speed = speed.speed < record.speed ? speed : record
		if(operator == 'slow') {
			speed = speed.speed > record.speed ? speed : record
		}
		speed[operator] = formatDistanceStrict(0, speed.speed * 1000)
		return speed
	}, arr[0])
}