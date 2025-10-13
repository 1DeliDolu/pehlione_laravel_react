// resources/js/wayfinder/index.ts
var queryParams = (options) => {
  if (!options || !options.query && !options.mergeQuery) {
    return "";
  }
  const query = options.query ?? options.mergeQuery;
  const includeExisting = options.mergeQuery !== void 0;
  const getValue = (value) => {
    if (value === true) {
      return "1";
    }
    if (value === false) {
      return "0";
    }
    return value.toString();
  };
  const params = new URLSearchParams(
    includeExisting && typeof window !== "undefined" ? window.location.search : ""
  );
  for (const key in query) {
    if (query[key] === void 0 || query[key] === null) {
      params.delete(key);
      continue;
    }
    if (Array.isArray(query[key])) {
      if (params.has(`${key}[]`)) {
        params.delete(`${key}[]`);
      }
      query[key].forEach((value) => {
        params.append(`${key}[]`, value.toString());
      });
    } else if (typeof query[key] === "object") {
      params.forEach((_, paramKey) => {
        if (paramKey.startsWith(`${key}[`)) {
          params.delete(paramKey);
        }
      });
      for (const subKey in query[key]) {
        if (typeof query[key][subKey] === "undefined") {
          continue;
        }
        if (["string", "number", "boolean"].includes(
          typeof query[key][subKey]
        )) {
          params.set(
            `${key}[${subKey}]`,
            getValue(query[key][subKey])
          );
        }
      }
    } else {
      params.set(key, getValue(query[key]));
    }
  }
  const str = params.toString();
  return str.length > 0 ? `?${str}` : "";
};

// resources/js/routes/index.ts
var login = (options) => ({
  url: login.url(options),
  method: "get"
});
login.definition = {
  methods: ["get", "head"],
  url: "/login"
};
login.url = (options) => {
  return login.definition.url + queryParams(options);
};
login.get = (options) => ({
  url: login.url(options),
  method: "get"
});
login.head = (options) => ({
  url: login.url(options),
  method: "head"
});
var loginForm = (options) => ({
  action: login.url(options),
  method: "get"
});
loginForm.get = (options) => ({
  action: login.url(options),
  method: "get"
});
loginForm.head = (options) => ({
  action: login.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
login.form = loginForm;
var logout = (options) => ({
  url: logout.url(options),
  method: "post"
});
logout.definition = {
  methods: ["post"],
  url: "/logout"
};
logout.url = (options) => {
  return logout.definition.url + queryParams(options);
};
logout.post = (options) => ({
  url: logout.url(options),
  method: "post"
});
var logoutForm = (options) => ({
  action: logout.url(options),
  method: "post"
});
logoutForm.post = (options) => ({
  action: logout.url(options),
  method: "post"
});
logout.form = logoutForm;
var home = (options) => ({
  url: home.url(options),
  method: "get"
});
home.definition = {
  methods: ["get", "head"],
  url: "/"
};
home.url = (options) => {
  return home.definition.url + queryParams(options);
};
home.get = (options) => ({
  url: home.url(options),
  method: "get"
});
home.head = (options) => ({
  url: home.url(options),
  method: "head"
});
var homeForm = (options) => ({
  action: home.url(options),
  method: "get"
});
homeForm.get = (options) => ({
  action: home.url(options),
  method: "get"
});
homeForm.head = (options) => ({
  action: home.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
home.form = homeForm;
var about = (options) => ({
  url: about.url(options),
  method: "get"
});
about.definition = {
  methods: ["get", "head"],
  url: "/about"
};
about.url = (options) => {
  return about.definition.url + queryParams(options);
};
about.get = (options) => ({
  url: about.url(options),
  method: "get"
});
about.head = (options) => ({
  url: about.url(options),
  method: "head"
});
var aboutForm = (options) => ({
  action: about.url(options),
  method: "get"
});
aboutForm.get = (options) => ({
  action: about.url(options),
  method: "get"
});
aboutForm.head = (options) => ({
  action: about.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
about.form = aboutForm;
var connection = (options) => ({
  url: connection.url(options),
  method: "get"
});
connection.definition = {
  methods: ["get", "head"],
  url: "/connection"
};
connection.url = (options) => {
  return connection.definition.url + queryParams(options);
};
connection.get = (options) => ({
  url: connection.url(options),
  method: "get"
});
connection.head = (options) => ({
  url: connection.url(options),
  method: "head"
});
var connectionForm = (options) => ({
  action: connection.url(options),
  method: "get"
});
connectionForm.get = (options) => ({
  action: connection.url(options),
  method: "get"
});
connectionForm.head = (options) => ({
  action: connection.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
connection.form = connectionForm;
var products = (options) => ({
  url: products.url(options),
  method: "get"
});
products.definition = {
  methods: ["get", "head"],
  url: "/products"
};
products.url = (options) => {
  return products.definition.url + queryParams(options);
};
products.get = (options) => ({
  url: products.url(options),
  method: "get"
});
products.head = (options) => ({
  url: products.url(options),
  method: "head"
});
var productsForm = (options) => ({
  action: products.url(options),
  method: "get"
});
productsForm.get = (options) => ({
  action: products.url(options),
  method: "get"
});
productsForm.head = (options) => ({
  action: products.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
products.form = productsForm;
var dashboard = (options) => ({
  url: dashboard.url(options),
  method: "get"
});
dashboard.definition = {
  methods: ["get", "head"],
  url: "/dashboard"
};
dashboard.url = (options) => {
  return dashboard.definition.url + queryParams(options);
};
dashboard.get = (options) => ({
  url: dashboard.url(options),
  method: "get"
});
dashboard.head = (options) => ({
  url: dashboard.url(options),
  method: "head"
});
var dashboardForm = (options) => ({
  action: dashboard.url(options),
  method: "get"
});
dashboardForm.get = (options) => ({
  action: dashboard.url(options),
  method: "get"
});
dashboardForm.head = (options) => ({
  action: dashboard.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
dashboard.form = dashboardForm;
var register = (options) => ({
  url: register.url(options),
  method: "get"
});
register.definition = {
  methods: ["get", "head"],
  url: "/register"
};
register.url = (options) => {
  return register.definition.url + queryParams(options);
};
register.get = (options) => ({
  url: register.url(options),
  method: "get"
});
register.head = (options) => ({
  url: register.url(options),
  method: "head"
});
var registerForm = (options) => ({
  action: register.url(options),
  method: "get"
});
registerForm.get = (options) => ({
  action: register.url(options),
  method: "get"
});
registerForm.head = (options) => ({
  action: register.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
register.form = registerForm;
export {
  about,
  connection,
  dashboard,
  home,
  login,
  logout,
  products,
  register
};
