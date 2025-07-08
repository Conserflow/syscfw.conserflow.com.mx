import js from "@eslint/js";
import vue from "eslint-plugin-vue";

export default [
  js.configs.recommended,
  {
    files: ["**/*.js"],
    languageOptions: {
      globals: {
        node: true,
      },
      parserOptions: {
        ecmaVersion: "latest",
        sourceType: "module",
      },
    },
    rules: {
      "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
      "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
    },
  },
  {
    files: ["**/*.vue"],
    plugins: {
      vue,
    },
    rules: vue.configs.recommended.rules,
  },
];
