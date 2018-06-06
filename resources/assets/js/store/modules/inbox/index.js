const state = {
  workflows: [],
};
const mutations = {
    pushDoc(state, workflow) {
        if (state.workflows.length > 0) {
            let index = state.workflows.findIndex(o => o.workflow_id == workflow.workflow_id);
            if (index >= 0) {
                let indexDoc = state.workflows[index].docs.findIndex(d => d.id == workflow.doc.id);
                if (indexDoc >= 0 && !workflow.doc.status) {
                  state.workflows[index].docs.splice(indexDoc,1);
                } else {
                  state.workflows[index].docs.push(workflow.doc);
                }
            }else{
                state.workflows.push({ workflow_id: workflow.workflow_id, docs: [workflow.doc] });
            }
        }else{
            state.workflows.push({workflow_id: workflow.workflow_id, docs:[workflow.doc] });
        }
    },
}
const getters = {
    getDataInbox: state => state
}
export default {
    state,
    mutations,
    getters,
}
