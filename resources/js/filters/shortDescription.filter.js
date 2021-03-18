export default function descriptionFilter(value){
    return value !== null ? (value.length > 50 ? value.substr(0, 100) + '...' : value) : ''
}
