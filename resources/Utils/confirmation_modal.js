import {Modal} from 'ant-design-vue';
import {h} from 'vue';

export const confirm_save = (callback = () => {}, title = null, content = null, okText = null) => {
    Modal.confirm({
        title: title ?? 'Confirmation',
        content: h('div', {}, [
            h('p', {}, [content ?? 'Voulez vous vraiment continuer l\'enregistrement ?'])
        ]),
        okText: okText ?? 'CONTINNUER',
        cancelText: 'ANNULER',
        okType: 'primary',
        icon: null,
        onOk() {
            callback();
        },
        onCancel() {

        },
    });
}

export const confirm_delete = (callback = () => {}, title = null, content = null, okText = null) => {
    Modal.confirm({
        title: title ?? 'Confirmation',
        content: h('div', {}, [
            h('p', {}, [content ?? 'Voulez vous vraiment continuer la suppression ?'])
        ]),
        okText: okText ?? 'SUPPRIMER',
        cancelText: 'ANNULER',
        okType: 'primary',
        okButtonProps: {danger: true},
        icon: null,
        onOk() {
            callback();
        },
        onCancel() {

        },
    });


}
export const confirm_status_change = (callback = () => {}, isActive = false, title = null, okText = null) => {
    const action = isActive ? 'désactiver' : 'activer';

    Modal.confirm({
        title: title ?? 'Confirmation',
        content: h('div', {}, [
            h('p', {}, [`Voulez-vous vraiment ${action} cet élément ?`])
        ]),
        okText: okText ?? action.toUpperCase(),
        cancelText: 'ANNULER',
        okType: 'primary',
        icon: null,
        onOk() {
            callback();
        },
    });
}


