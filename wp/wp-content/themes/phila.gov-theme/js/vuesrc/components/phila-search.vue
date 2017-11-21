<template>
  <form
    v-on:submit.prevent="onSubmit">
    <div class="search">
      <input class="search-field"
      type="text"
      name="search"
      :id="id"
      :placeholder="placeholder"
      :aria-label="search"
      v-model="search">
      <input
      type="submit"
      value="submit"
      class="search-submit">
    </div>
  </form>
</template>

<script>
export default {
  name: 'phila-search',
  data: function () {
    return {
      search: ''
    }
  },
  props: {
    id: String,
    placeholder: String,
    ariaLabel: {
      type: String,
      default: 'Search'
    },
  },
  methods: {
    onSubmit: function (event) {
      return this.search
    }
  }
}
</script>
