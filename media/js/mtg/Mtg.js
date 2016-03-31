var Mtg = (function() {

    var namespace = function(namespace) {
        var parts = namespace.split('.'),
            parent = Mtg,
            i;

        if(parts[0] === "Mtg") {
            parts = parts.slice(1);
        }

        for(i = 0; i < parts.length; i += 1) {
            if(typeof(parent[parts[i]]) === "undefined") {
                parent[parts[i]] = {};
            }
            parent = parent[parts[i]];
        }
        return parent;
    };

    return {
        namespace: namespace
    };
}());