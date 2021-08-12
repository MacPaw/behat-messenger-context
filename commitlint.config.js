module.exports = {
    extends: ['@commitlint/config-conventional'],
    rules: {
        'scope-case': [
            2,
            'never',
            [],
        ],
        'header-max-length': [2, 'always', 300],
        'subject-case': [
            2,
            'never',
            ['pascal-case', 'camel-case', 'snake-case', 'kebab-case'],
        ],
    }
}
