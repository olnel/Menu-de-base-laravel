import dayjs from "dayjs";

const formatDate = (dateString) => {
    return dateString ? dayjs(dateString).format(dateFormat) : '-';
};

const formatDateTime = (dateString) => {
    return dateString ? dayjs(dateString).format(dateTimeFormat) : '-';
};

export default [formatDate(), formatDateTime()]
