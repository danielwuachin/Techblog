const NAME = "Techblog",
    DOMAIN = `http://api-blog.6te.net`,
    PUBLICATIONS = `${DOMAIN}/publications`,
    PUBLICATION = `${DOMAIN}/publications?id=`,
    CATEGORIES = `${DOMAIN}/category`,
    CATEGORY = `${DOMAIN}/category?id=`,
    PLATFORMS = `${DOMAIN}/platform`,
    PLATFORM = `${DOMAIN}/platform?id=`,
    USERS = `${DOMAIN}/users`,
    USER = `${DOMAIN}/users?id=`,
    COMMENTS = `${DOMAIN}/comments`,
    COMMENT = `${DOMAIN}/comments?id=`,
    SUBCOMMENTS = `${DOMAIN}/subcomments`,
    SUBCOMMENT = `${DOMAIN}/subcomments?id=`,
    SEARCH = `${DOMAIN}/search?search=`,
    AUTH = `${DOMAIN}/auth`,
    LOGOUT = `${DOMAIN}/logout`;
    

let page = 1;

export default {
    NAME,
    DOMAIN,
    PUBLICATIONS,
    PUBLICATION,
    CATEGORIES,
    CATEGORY,
    PLATFORMS,
    PLATFORM,
    USERS,
    USER,
    COMMENTS,
    COMMENT,
    SUBCOMMENTS,
    SUBCOMMENT,
    SEARCH,
    AUTH,
    LOGOUT,
    page
}