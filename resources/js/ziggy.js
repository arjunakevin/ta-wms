const Ziggy = {"url":"http:\/\/wms.test","port":null,"defaults":{},"routes":{"products.index":{"uri":"products","methods":["GET","HEAD"]},"products.create":{"uri":"products\/create","methods":["GET","HEAD"]},"products.store":{"uri":"products","methods":["POST"]},"products.show":{"uri":"products\/{product}","methods":["GET","HEAD"],"bindings":{"product":"id"}},"products.edit":{"uri":"products\/{product}\/edit","methods":["GET","HEAD"]},"products.update":{"uri":"products\/{product}","methods":["PUT","PATCH"]},"products.destroy":{"uri":"products\/{product}","methods":["DELETE"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
