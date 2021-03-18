export default function nameFilter(value){
    return value.replace(/(.+) (.).+ (.).+/, '$1 $2. $3.');
}
