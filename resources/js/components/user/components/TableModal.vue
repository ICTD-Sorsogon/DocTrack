<template>
<v-dialog v-model="dialog" persistent scrollable max-width="1000px">
      <v-card v-if="selected_document">
        <v-container>
          <v-row>
            <v-col cols="6" sm="6">
              <v-card-title primary-title> Document Details </v-card-title>
            </v-col>
            <v-col cols="6" sm="6">
                <v-card-actions class="mr-4">
                    <v-spacer></v-spacer>
                    <v-btn x-large color="gray" @click="$emit('closeDialog')" icon>
                    <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-col>
          </v-row>
        </v-container>
        <v-card-text>
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Tracking ID</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>
                      <v-chip
                        label
                        dark
                        :color="selected_document.color"
                        id="document_label"
                      >
                        {{ selected_document.tracking_code }}
                      </v-chip>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Subject</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.subject
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Source</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title v-if="selected_document.is_external"
                      >External</v-list-item-title
                    >
                    <v-list-item-title v-else>Internal</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Type</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.document_type.name
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Originating Office</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{ originating_office_name }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Destination Office</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>
                    <v-tooltip :key="destination.office_code" v-for="destination in selected_document.destination_office_id" top>
                      <template v-slot:activator="{ on, attrs }">
                        <v-chip v-bind="attrs" v-on="on" >
                          {{destination.office_code}}
                        </v-chip>
                      </template>
                      <span>{{destination.name}}</span>
                    </v-tooltip>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Sender</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.sender_name
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Status</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.status.replace(/\w/, val=>val.toUpperCase())
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Date Filed</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.created_at
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col cols="12" xl="4" lg="4" md="4" sm="12">
              <v-list flat subheader>
                <v-subheader>Page Count</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.page_count
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
            <v-col cols="12" xl="4" lg="4" md="4" sm="12">
              <v-list flat subheader>
                <v-subheader>Attachment Page Count</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.attachment_page_count
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
            <v-col cols="12" xl="4" lg="4" md="4" sm="12">
              <v-list flat subheader>
                <v-subheader>Terminal</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title v-if="selected_document.is_terminal"
                      >Yes</v-list-item-title
                    >
                    <v-list-item-title v-else>No</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-divider inset />
          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Remarks</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_document.remarks
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-subheader>History</v-subheader>
    <v-timeline
        align-top
        dense
      >
        <v-timeline-item
          small
          v-for="{id,action, created_at, remarks} in selected_document.tracking_records"
          :color="dotColor(action)"
          :key="id"
        >
          <v-row class="pt-1">
            <v-col cols="3">
              <strong>{{ created_at }}</strong>
            </v-col>
            <v-col>
              <strong>{{ action.toUpperCase() }}</strong>
              <div class="caption">
                {{ remarks }}
              </div>
            </v-col>
             <v-col>
              <v-avatar>
                <v-img
                  src="https://avataaars.io/?avatarStyle=Circle&topType=LongHairFrida&accessoriesType=Kurt&hairColor=Red&facialHairType=BeardLight&facialHairColor=BrownDark&clotheType=GraphicShirt&clotheColor=Gray01&graphicType=Skull&eyeType=Wink&eyebrowType=RaisedExcitedNatural&mouthType=Disbelief&skinColor=Brown"
                ></v-img>
              </v-avatar>
              <v-avatar>
                <v-img
                  src="https://avataaars.io/?avatarStyle=Circle&topType=ShortHairFrizzle&accessoriesType=Prescription02&hairColor=Black&facialHairType=MoustacheMagnum&facialHairColor=BrownDark&clotheType=BlazerSweater&clotheColor=Black&eyeType=Default&eyebrowType=FlatNatural&mouthType=Default&skinColor=Tanned"
                ></v-img>
              </v-avatar>
              <v-avatar>
                <v-img
                  src="https://avataaars.io/?avatarStyle=Circle&topType=LongHairMiaWallace&accessoriesType=Sunglasses&hairColor=BlondeGolden&facialHairType=Blank&clotheType=BlazerSweater&eyeType=Surprised&eyebrowType=RaisedExcited&mouthType=Smile&skinColor=Pale"
                ></v-img>
              </v-avatar>
            </v-col>
          </v-row>
        </v-timeline-item>
      </v-timeline>
        </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
</template>

<script>
    export default {
    props: ['selected_document', 'dialog'],
    computed: {
      originating_office_name({selected_document}) {
        return selected_document.origin_office?.name ?? selected_document.originating_office
      },
      sender_name({selected_document}) {
        return selected_document.sender?.name ?? selected_document.sender_name
      },
    },
    methods: {
      dotColor(action){
        let colors = {terminated: 'grey', rejected:'pink', created: 'cyan', acknowledged: 'deep-purple', recieved: 'teal'}
        return colors[action] ?? 'amber'
      }
    },
  }
</script>

<style>

</style>
