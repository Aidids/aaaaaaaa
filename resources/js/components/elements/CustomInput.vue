<template>
    <div :class="this.class" class="my-1">
        <label class="form-label text-capitalize">
            {{ this.label }}
            <span v-if="this.required" class="text-danger">*</span>
        </label>
        <textarea v-if="this.type === 'textarea'" v-model="inputValue" type="textarea"
        :placeholder="this.placeholder" :maxlength="this.maxLength" class="form-control"
        :disabled="this.disabled" style="resize: none;" required>
        </textarea>
        <input class="form-control" v-else :type="this.type" v-model="inputValue" :disabled="this.disabled"
        :maxlength="this.maxLength" :placeholder="this.placeholder">
        <p v-if="this.max" class="text-danger mb-0">Maximum characters reached. (Limit {{ this.maxLength }})</p>
    </div>
</template>
<script>
export default {
    props: ['label', 'class', 'type', 'placeholder', 'maxLength', 'required', 'modelValue', 'disabled'],
    data(){
        return {
            max: false,
        }
    },
    computed: {
        inputValue: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        }
    },
    watch: {
        modelValue(text){
            if (text !== null) {
                if(text.length === parseInt(this.maxLength)) {
                    this.max = true;
                } else {
                    this.max = false;
                }
            }
        }
    }
}
</script>
