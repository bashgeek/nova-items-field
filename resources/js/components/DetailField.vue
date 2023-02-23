<template>
    <PanelItem :field="field" >
        <template #value>
            <ul>
                <li class="mb-1" v-for="value of fieldValue">
                    <span class="text-gray-300 mr-2">&dash;</span>{{ value }}
                </li>
            </ul>
        </template>
    </PanelItem>
</template>

<script>
import HasFieldValue from "../mixins/HasFieldValue";

export default {
    mixins: [HasFieldValue],

    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
