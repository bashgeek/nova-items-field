export default {
    computed: {
        fieldValue() {
            let fieldValue = this.field.value;
            while (typeof fieldValue === 'string') {
                try {
                    fieldValue = JSON.parse(fieldValue);
                } catch {
                    fieldValue = null;
                }
            }
            if (! fieldValue) {
                fieldValue = [];
            }
            return fieldValue;
        }
    }
};
