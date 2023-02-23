<template>
    <div>
        <span v-if="fieldValue">{{ fieldValue.length }}</span>
        <span v-else>0</span>
    </div>
</template>

<script>
import HasFieldValue from "../mixins/HasFieldValue";

export default {
    mixins: [HasFieldValue],

    props: ['resourceName', 'field'],
}
</script>
