import {router, usePage} from "@inertiajs/vue3";
import dayjs from "dayjs";

export const reloadPage = (filters) => {
    const filt = {};
    Object.entries(filters).forEach(([cle, valeur]) => {
        if(valeur !== null && valeur !== '') filt[cle] = valeur;
    });

    router.get(
        usePage().props.ziggy.location,
        { ...filt },
        { preserveState: true, preserveScroll: true }
    );
}


export const typeReponse = (type) => {
    switch (type) {
        case "QCM": return "QCM";
        case "VRAI_FAUX": return "VRAI ou FAUX";
        case "REPONSE_COURTE": return "REPONSE COURTE";
    }
    return "-";
}

export function getBase64(img, callback) {
    const reader = new FileReader();
    reader.addEventListener('load', () => callback(reader.result));
    reader.readAsDataURL(img);
}

export  function base64ToBlob(base64, mimeType) {
    const byteString = atob(base64.split(",")[1]);
    const arrayBuffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(arrayBuffer);
    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }
    return new Blob([arrayBuffer], { type: mimeType });
}

export const extractError = (object) => {
    if(!object) return null;
    if(object instanceof String) return object;

    var text = [];
    Object.entries(object).forEach(([cle, valeur]) => {
        if(valeur) text.push(valeur);
    });
    return text.length ? text.join(' | ') : null;
}

export function formatDate(date) {
    return dayjs(date).format("DD/MM/YYYY");
}

export function formatDateTime(date) {
    return dayjs(date).format("DD/MM/YYYY HH:mm:ss");
}
