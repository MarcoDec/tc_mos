module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true,
        node: true
    },
    /* extends: [
        'plugin:vue/vue3-recommended',
        'eslint:recommended',
        '@vue/typescript/recommended'
    ], */
    parserOptions: {
        ecmaVersion: 2021
    },
    // plugins: [],
    rules: {
        // https://eslint.org/docs/rules/
        // Possible Errors
        'for-direction': 'error',
        'getter-return': 'error',
        'no-async-promise-executor': 'error',
        'no-await-in-loop': 'error',
        'no-compare-neg-zero': 'error',
        'no-cond-assign': 'error',
        'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
        'no-constant-condition': 'error',
        'no-control-regex': 'error',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
        'no-dupe-args': 'error',
        'no-dupe-else-if': 'error',
        'no-dupe-keys': 'error',
        'no-duplicate-case': 'error',
        'no-empty': 'error',
        'no-empty-character-class': 'error',
        'no-ex-assign': 'error',
        'no-extra-boolean-cast': 'error',
        'no-extra-parens': 'error',
        'no-extra-semi': 'error',
        'no-func-assign': 'error',
        'no-import-assign': 'error',
        'no-inner-declarations': 'error',
        'no-invalid-regexp': 'error',
        'no-irregular-whitespace': 'error',
        'no-loss-of-precision': 'error',
        'no-misleading-character-class': 'error',
        'no-obj-calls': 'error',
        'no-promise-executor-return': 'error',
        'no-prototype-builtins': 'error',
        'no-regex-spaces': 'error',
        'no-setter-return': 'error',
        'no-sparse-arrays': 'error',
        'no-template-curly-in-string': 'error',
        'no-unexpected-multiline': 'error',
        'no-unreachable': 'error',
        'no-unreachable-loop': 'error',
        'no-unsafe-finally': 'error',
        'no-unsafe-negation': 'error',
        'no-unsafe-optional-chaining': 'error',
        'no-useless-backreference': 'error',
        'require-atomic-updates': 'error',
        'use-isnan': 'error',
        'valid-typeof': 'error',
        // Best Practices
        'array-callback-return': 'error',
        'block-scoped-var': 'error',
        'class-methods-use-this': 'error',
        'complexity': 'error',
        'consistent-return': 'error',
        'default-case-last': 'error',
        'default-param-last': 'error',
        'dot-location': ['error', 'property'],
        'dot-notation': 'error',
        'eqeqeq': 'error',
        'grouped-accessor-pairs': 'error',
        'max-classes-per-file': 'error',
        'no-alert': 'error',
        'no-caller': 'error',
        'no-case-declarations': 'error',
        'no-constructor-return': 'error',
        'no-div-regex': 'error',
        'no-else-return': ['error', {allowElseIf: false}],
        'no-empty-function': 'error',
        'no-empty-pattern': 'error',
        'no-eval': 'error',
        'no-extend-native': 'error',
        'no-extra-bind': 'error',
        'no-fallthrough': 'error',
        'no-floating-decimal': 'error',
        'no-global-assign': 'error',
        'no-implicit-coercion': 'error',
        'no-implicit-globals': 'error',
        'no-implied-eval': 'error',
        'no-invalid-this': 'error',
        'no-labels': 'error',
        'no-lone-blocks': 'error',
        'no-loop-func': 'error',
        'no-multi-spaces': 'error',
        'no-multi-str': 'error',
        'no-new': 'error',
        'no-new-func': 'error',
        'no-new-wrappers': 'error',
        'no-nonoctal-decimal-escape': 'error',
        'no-octal': 'error',
        'no-octal-escape': 'error',
        'no-param-reassign': 'error',
        'no-proto': 'error',
        'no-redeclare': 'error',
        'no-restricted-properties': 'error',
        'no-return-assign': 'error',
        'no-return-await': 'error',
        'no-script-url': 'error',
        'no-self-assign': 'error',
        'no-self-compare': 'error',
        'no-sequences': 'error',
        'no-unmodified-loop-condition': 'error',
        'no-unused-expressions': 'error',
        'no-useless-call': 'error',
        'no-useless-catch': 'error',
        'no-useless-concat': 'error',
        'no-useless-escape': 'error',
        'no-useless-return': 'error',
        'no-void': 'error',
        'no-with': 'error',
        'prefer-regex-literals': 'error',
        // Variables
        'init-declarations': 'error',
        'no-delete-var': 'error',
        'no-shadow': 'error',
        'no-shadow-restricted-names': 'error',
        'no-undef': 'error',
        'no-undef-init': 'error',
        'no-undefined': 'error',
        'no-unused-vars': 'error',
        'no-use-before-define': 'error',
        // Stylistic Issues
        'array-bracket-newline': ['error', 'consistent'],
        'array-bracket-spacing': 'error',
        'array-element-newline': ['error', 'consistent'],
        'block-spacing': 'error',
        'brace-style': 'error',
        'camelcase': 'error',
        'comma-dangle': 'error',
        'comma-spacing': 'error',
        'comma-style': 'error',
        'computed-property-spacing': 'error',
        'eol-last': 'error',
        'func-call-spacing': 'error',
        'func-name-matching': 'error',
        'func-style': ['error', 'declaration', {'allowArrowFunctions': true}],
        'function-call-argument-newline': ['error', 'consistent'],
        'function-paren-newline': ['error', 'consistent'],
        'indent': ['error', 4],
        'key-spacing': 'error',
        'keyword-spacing': 'error',
        'linebreak-style': 'error',
        'lines-between-class-members': 'error',
        'new-cap': 'error',
        'new-parens': 'error',
        'no-array-constructor': 'error',
        'no-bitwise': 'error',
        'no-continue': 'error',
        'no-lonely-if': 'error',
        'no-mixed-spaces-and-tabs': 'error',
        'no-multi-assign': 'error',
        'no-multiple-empty-lines': 'error',
        'no-nested-ternary': 'error',
        'no-new-object': 'error',
        'no-tabs': 'error',
        'no-trailing-spaces': 'error',
        'no-underscore-dangle': 'error',
        'no-unneeded-ternary': 'error',
        'no-whitespace-before-property': 'error',
        'object-curly-newline': ['error', {consistent: true}],
        'object-curly-spacing': 'error',
        'operator-assignment': 'error',
        'operator-linebreak': ['error', 'before'],
        'quotes': ['error', 'single'],
        'semi': ['error', 'never'],
        'sort-keys': 'error',
        'space-before-function-paren': ['error', 'never'],
        'space-in-parens': 'error',
        'space-infix-ops': 'error',
        'space-unary-ops': 'error',
        'switch-colon-spacing': 'error',
        // ECMAScript 6
        'arrow-body-style': 'error',
        'arrow-parens': ["error", "as-needed"],
        'arrow-spacing': 'error',
        'constructor-super': 'error',
        'no-class-assign': 'error',
        'no-confusing-arrow': 'error',
        'no-const-assign': 'error',
        'no-dupe-class-members': 'error',
        'no-duplicate-imports': 'error',
        'no-new-symbol': 'error',
        'no-this-before-super': 'error',
        'no-useless-computed-key': 'error',
        'no-useless-constructor': 'error',
        'no-useless-rename': 'error',
        'no-var': 'error',
        'object-shorthand': 'error',
        'prefer-arrow-callback': 'error',
        'prefer-const': 'error',
        'prefer-rest-params': 'error',
        'prefer-template': 'error',
        'rest-spread-spacing': 'error',
        'sort-imports': 'error',
        'template-curly-spacing': 'error'
    }
}
